const variantOptions = Array.from(document.querySelectorAll("[data-order-variant]"));
const productOptions = Array.from(document.querySelectorAll("[data-order-option]"));
const orderPage = document.querySelector("[data-product-order]");
const orderFields = Array.from(document.querySelectorAll("[data-order-field]"));
const paymentOptions = Array.from(document.querySelectorAll("[data-order-payment]"));
const providerOptions = Array.from(document.querySelectorAll("[data-order-provider]"));
const paymentPanels = Array.from(document.querySelectorAll("[data-payment-panel]"));
const orderEmpty = document.querySelector("[data-order-empty]");
const orderDetails = document.querySelector("[data-order-details]");
const selectedVariant = document.querySelector("[data-summary-variant]");
const selectedPayment = document.querySelector("[data-summary-payment]");
const selectedPrice = document.querySelector("[data-summary-price]");
const summaryFields = document.querySelector("[data-summary-fields]");
const checkout = document.querySelector("[data-order-checkout]");
const message = document.querySelector("[data-order-message]");
const confirmDialog = document.querySelector("[data-order-confirm]");
const confirmSummary = document.querySelector("[data-order-confirm-summary]");
let activeVariant = null;
let activeProductOption = null;

const formatRupiah = (amount) => new Intl.NumberFormat("id-ID", {
  style: "currency",
  currency: "IDR",
  maximumFractionDigits: 0,
}).format(amount);

const getFieldValues = () => orderFields.map((field) => {
  let value = field.value.trim();
  if (field.hasAttribute("data-username-field")) {
    value = value.replace(/^@+/, "").trim();
    value = value ? `@${value}` : "";
  }
  return { label: field.dataset.fieldLabel || field.name, value, required: field.required };
});

const getPayment = () => {
  const method = paymentOptions.find((option) => option.checked);
  if (!method) return "";
  if (method.value === "QRIS") return "QRIS";
  const provider = providerOptions.find((option) => option.checked);
  return provider ? `${method.value} - ${provider.value}` : "";
};

const isComplete = () => Boolean(
  activeVariant && getPayment() && (!productOptions.length || activeProductOption)
    && getFieldValues().every((field) => !field.required || field.value)
    && orderFields.every((field) => field.type !== "email" || field.validity.valid),
);

const updatePaymentPanels = () => {
  const method = paymentOptions.find((option) => option.checked)?.value;
  paymentPanels.forEach((panel) => { panel.hidden = panel.dataset.paymentPanel !== method; });
  const qrisNote = document.querySelector("[data-qris-note]");
  if (qrisNote) qrisNote.hidden = method !== "QRIS";
};

const updateSummary = () => {
  const hasSelection = Boolean(activeVariant);
  orderEmpty.hidden = hasSelection;
  orderDetails.hidden = !hasSelection;
  checkout.disabled = !isComplete();
  message.textContent = "";

  if (activeVariant) {
    selectedVariant.textContent = activeVariant.dataset.variantName;
    selectedPrice.textContent = formatRupiah(Number(activeVariant.dataset.price));
    selectedPayment.textContent = getPayment() || "Pilih pembayaran";
  }
  const selectedOption = document.querySelector("[data-summary-option]");
  if (selectedOption) selectedOption.textContent = activeProductOption?.dataset.optionName || "—";

  summaryFields.replaceChildren();
  getFieldValues().filter((field) => field.value || field.required).forEach((field) => {
    const row = document.createElement("div");
    const label = document.createElement("span");
    const value = document.createElement("strong");
    row.className = "order-detail-row order-summary-field";
    label.className = "order-summary-field-label";
    value.className = "order-summary-field-value";
    label.textContent = field.label;
    value.textContent = field.value || "Belum diisi";
    row.append(label, value);
    summaryFields.append(row);
  });
};

const addConfirmRow = (label, value) => {
  const term = document.createElement("dt");
  const description = document.createElement("dd");
  term.textContent = label;
  description.textContent = value;
  confirmSummary.append(term, description);
};

variantOptions.forEach((option) => option.addEventListener("click", () => {
  if (option.hidden) return;
  activeVariant = option;
  variantOptions.forEach((item) => item.setAttribute("aria-pressed", String(item === option)));
  updateSummary();
}));

productOptions.forEach((option) => option.addEventListener("click", () => {
  activeProductOption = option;
  productOptions.forEach((item) => item.setAttribute("aria-pressed", String(item === option)));
  let matchingVariants = 0;
  variantOptions.forEach((variant) => {
    const matches = variant.dataset.optionId === option.dataset.optionId;
    variant.hidden = !matches;
    if (matches) matchingVariants += 1;
    if (!matches) variant.setAttribute("aria-pressed", "false");
  });
  const emptyOptionMessage = document.querySelector("[data-option-empty]");
  if (emptyOptionMessage) emptyOptionMessage.hidden = matchingVariants > 0;
  activeVariant = null;
  variantOptions.forEach((variant) => variant.setAttribute("aria-pressed", "false"));
  updateSummary();
}));

orderFields.forEach((field) => {
  if (field.hasAttribute("data-username-field")) {
    field.addEventListener("focus", () => {
      if (!field.value.trim()) {
        field.value = "@";
        if (["text", "search", "url", "tel", "password"].includes(field.type)) field.setSelectionRange(1, 1);
      }
    });
    field.addEventListener("input", () => {
      const cursor = field.selectionStart || 0;
      const hadPrefix = field.value.trimStart().startsWith("@");
      const username = field.value.trim().replace(/^@+/, "").trim();
      field.value = username ? `@${username}` : "";
      if (username && !hadPrefix && ["text", "search", "url", "tel", "password"].includes(field.type)) {
        field.setSelectionRange(cursor + 1, cursor + 1);
      }
      updateSummary();
    });
    field.addEventListener("blur", () => {
      if (field.value.trim() === "@") field.value = "";
      updateSummary();
    });
  } else {
    field.addEventListener("input", updateSummary);
  }
});

paymentOptions.forEach((option) => option.addEventListener("change", () => {
  providerOptions.forEach((provider) => { provider.checked = false; });
  updatePaymentPanels();
  updateSummary();
}));
providerOptions.forEach((option) => option.addEventListener("change", updateSummary));

checkout.addEventListener("click", () => {
  if (!isComplete()) return;
  confirmSummary.replaceChildren();
  addConfirmRow("Produk", orderPage.dataset.productName);
  addConfirmRow("Pilihan", orderPage.dataset.groupName);
  if (activeProductOption) addConfirmRow("Subpilihan", activeProductOption.dataset.optionName);
  addConfirmRow("Paket", activeVariant.dataset.variantName);
  getFieldValues().filter((field) => field.value).forEach((field) => addConfirmRow(field.label, field.value));
  addConfirmRow("Pembayaran", getPayment());
  addConfirmRow("Total", formatRupiah(Number(activeVariant.dataset.price)));
  confirmDialog.showModal();
});

confirmDialog.querySelector("[data-order-cancel]").addEventListener("click", () => confirmDialog.close());
confirmDialog.querySelector("[data-order-submit]").addEventListener("click", async (event) => {
  if (!isComplete()) {
    confirmDialog.close();
    return;
  }

  const submitButton = event.currentTarget;
  submitButton.disabled = true;
  submitButton.textContent = "Menyimpan pesanan...";
  message.textContent = "Menyimpan pesanan sebelum membuka WhatsApp...";

  const fields = Object.fromEntries(orderFields.map((field) => [field.name, field.value.trim()]));
  try {
    const response = await fetch(orderPage.dataset.orderUrl, {
      method: "POST",
      headers: {
        "Content-Type": "application/json",
        "Accept": "application/json",
        "X-CSRF-TOKEN": orderPage.dataset.csrfToken,
      },
      body: JSON.stringify({
        variant_id: Number(activeVariant.dataset.variantId),
        payment_method: getPayment(),
        fields,
      }),
    });
    const result = await response.json();
    if (!response.ok) {
      const validationMessage = Object.values(result.errors || {}).flat()[0];
      throw new Error(validationMessage || result.message || "Pesanan belum dapat disimpan.");
    }
    window.location.assign(result.whatsapp_url);
  } catch (error) {
    message.textContent = error.message || "Terjadi kesalahan saat menyimpan pesanan.";
    confirmDialog.close();
  } finally {
    submitButton.disabled = false;
    submitButton.textContent = "Konfirmasi & buka WhatsApp";
  }
});

updatePaymentPanels();
updateSummary();
