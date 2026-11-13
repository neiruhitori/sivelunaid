const variantOptions = Array.from(document.querySelectorAll("[data-order-variant]"));
const productOptions = Array.from(document.querySelectorAll("[data-order-option]"));
const orderPage = document.querySelector("[data-product-order]");
const orderFields = Array.from(document.querySelectorAll("[data-order-field]"));
const paymentOptions = Array.from(document.querySelectorAll("[data-order-payment]"));
const providerOptions = Array.from(document.querySelectorAll("[data-order-provider]"));
const paymentPanels = Array.from(document.querySelectorAll("[data-payment-panel]"));
const orderEmpty = document.querySelector("[data-order-empty]");
const orderDetails = document.querySelector("[data-order-details]");
const selectedPayment = document.querySelector("[data-summary-payment]");
const selectedPrice = document.querySelector("[data-summary-price]");
const selectedTotalLabel = document.querySelector("[data-summary-total-label]");
const selectedPackage = document.querySelector("[data-summary-selected]");
const selectedPackageName = document.querySelector("[data-summary-selected-name]");
const selectedPackagePrice = document.querySelector("[data-summary-selected-price]");
const summaryItems = document.querySelector("[data-summary-items]");
const summaryFields = document.querySelector("[data-summary-fields]");
const checkout = document.querySelector("[data-order-checkout]");
const message = document.querySelector("[data-order-message]");
const cartAddButtons = Array.from(document.querySelectorAll("[data-cart-add-variant]"));
const variantGrid = document.querySelector(".product-variant-grid");
const confirmDialog = document.querySelector("[data-order-confirm]");
const confirmSummary = document.querySelector("[data-order-confirm-summary]");
const cart = new Map();
let activeVariant = null;
let activeProductOption = null;
let hasSelectedVariant = false;

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

const cartTotal = () => Array.from(cart.values()).reduce((total, item) => total + item.price * item.quantity, 0);

const cartItemFromVariant = (variant) => ({
  id: Number(variant.dataset.variantId),
  name: variant.dataset.variantName,
  price: Number(variant.dataset.price),
  optionId: variant.dataset.optionId || null,
  optionName: activeProductOption?.dataset.optionName || "",
  quantity: 1,
});

const isComplete = () => Boolean(
  cart.size > 0 && getPayment()
    && getFieldValues().every((field) => !field.required || field.value)
    && orderFields.every((field) => field.type !== "email" || field.validity.valid),
);

const updatePaymentPanels = () => {
  const method = paymentOptions.find((option) => option.checked)?.value;
  paymentPanels.forEach((panel) => { panel.hidden = panel.dataset.paymentPanel !== method; });
  const qrisNote = document.querySelector("[data-qris-note]");
  if (qrisNote) qrisNote.hidden = method !== "QRIS";
};

const makeCartRow = (item) => {
  const row = document.createElement("div");
  row.className = "order-cart-item";
  const description = document.createElement("div");
  description.className = "order-cart-item-description";
  const name = document.createElement("strong");
  name.textContent = item.optionName ? `${item.optionName} - ${item.name}` : item.name;
  const price = document.createElement("small");
  price.textContent = `${formatRupiah(item.price)} / item`;
  description.append(name, price);

  const controls = document.createElement("div");
  controls.className = "order-cart-item-controls";
  const makeControl = (label, action, text) => {
    const button = document.createElement("button");
    button.type = "button";
    button.className = "cart-quantity-button";
    button.setAttribute("aria-label", label);
    button.dataset.cartAction = action;
    button.dataset.cartId = String(item.id);
    button.textContent = text;
    return button;
  };
  controls.append(makeControl(`Kurangi ${item.name}`, "decrease", "-"));
  const quantity = document.createElement("span");
  quantity.className = "cart-quantity";
  quantity.textContent = String(item.quantity);
  controls.append(quantity, makeControl(`Tambah ${item.name}`, "increase", "+"));

  const subtotal = document.createElement("strong");
  subtotal.className = "order-cart-item-subtotal";
  subtotal.textContent = formatRupiah(item.price * item.quantity);
  const remove = makeControl(`Hapus ${item.name} dari keranjang`, "remove", "x");
  remove.classList.add("cart-remove-button");
  controls.append(subtotal, remove);
  row.append(description, controls);
  return row;
};

const updateSummary = () => {
  const hasItems = cart.size > 0;
  const pendingSelection = activeVariant && !cart.has(Number(activeVariant.dataset.variantId));
  const hasSummary = hasItems || Boolean(activeVariant);
  orderEmpty.hidden = hasSummary;
  orderDetails.hidden = !hasSummary;
  checkout.disabled = !isComplete();
  message.textContent = "";
  if (variantGrid) variantGrid.classList.toggle("has-selection", hasSelectedVariant);
  cartAddButtons.forEach((button) => {
    const item = cart.get(Number(button.dataset.variantId));
    const count = button.querySelector(".variant-add-count");
    if (count) count.textContent = item ? String(item.quantity) : "";
    button.classList.toggle("has-count", Boolean(item));
    button.setAttribute("aria-label", item
      ? `Tambah satu ${button.dataset.variantName || "paket"} lagi ke keranjang, jumlah sekarang ${item.quantity}`
      : `Tambah ${button.dataset.variantName || "paket"} ke keranjang`);
  });

  summaryItems.replaceChildren();
  Array.from(cart.values()).forEach((item) => summaryItems.append(makeCartRow(item)));
  selectedPackage.hidden = !pendingSelection;
  if (pendingSelection) {
    const optionName = activeProductOption?.dataset.optionName;
    selectedPackageName.textContent = optionName ? `${optionName} - ${activeVariant.dataset.variantName}` : activeVariant.dataset.variantName;
    selectedPackagePrice.textContent = formatRupiah(Number(activeVariant.dataset.price));
  }
  selectedTotalLabel.textContent = hasItems ? "Total keranjang" : "Harga terpilih";
  selectedPrice.textContent = formatRupiah(hasItems ? cartTotal() : (activeVariant ? Number(activeVariant.dataset.price) : 0));
  selectedPayment.textContent = getPayment() || "Pilih pembayaran";

  const selectedOption = document.querySelector("[data-summary-option]");
  if (selectedOption) {
    const optionNames = [...new Set([
      ...Array.from(cart.values()).map((item) => item.optionName),
      activeProductOption?.dataset.optionName,
    ].filter(Boolean))];
    selectedOption.textContent = optionNames.length ? optionNames.join(", ") : "—";
  }

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
  hasSelectedVariant = true;
  variantOptions.forEach((item) => {
    item.setAttribute("aria-pressed", String(item === option));
    item.closest(".variant-option-card").classList.toggle("is-selected", item === option);
  });
  updateSummary();
}));

productOptions.forEach((option) => option.addEventListener("click", () => {
  activeProductOption = option;
  productOptions.forEach((item) => item.setAttribute("aria-pressed", String(item === option)));
  let matchingVariants = 0;
  variantOptions.forEach((variant) => {
    const matches = variant.dataset.optionId === option.dataset.optionId;
    variant.closest(".variant-option-card").hidden = !matches;
    if (matches) matchingVariants += 1;
    variant.setAttribute("aria-pressed", "false");
    variant.closest(".variant-option-card").classList.remove("is-selected");
  });
  const emptyOptionMessage = document.querySelector("[data-option-empty]");
  if (emptyOptionMessage) emptyOptionMessage.hidden = matchingVariants > 0;
  activeVariant = null;
  updateSummary();
}));

cartAddButtons.forEach((button) => button.addEventListener("click", () => {
  const variant = button.closest(".variant-option-card")?.querySelector("[data-order-variant]");
  if (!variant || button.closest(".variant-option-card").hidden) return;
  const id = Number(variant.dataset.variantId);
  const current = cart.get(id);
  if (current) {
    current.quantity = Math.min(current.quantity + 1, 99);
  } else {
    cart.set(id, cartItemFromVariant(variant));
  }
  updateSummary();
}));

summaryItems.addEventListener("click", (event) => {
  const button = event.target.closest("[data-cart-action]");
  if (!button) return;
  const id = Number(button.dataset.cartId);
  const item = cart.get(id);
  if (!item) return;
  if (button.dataset.cartAction === "remove") cart.delete(id);
  if (button.dataset.cartAction === "increase") item.quantity = Math.min(item.quantity + 1, 99);
  if (button.dataset.cartAction === "decrease") {
    item.quantity -= 1;
    if (item.quantity <= 0) cart.delete(id);
  }
  updateSummary();
});

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
  Array.from(cart.values()).forEach((item, index) => {
    const itemName = item.optionName ? `${item.optionName} - ${item.name}` : item.name;
    addConfirmRow(`Item ${index + 1}`, `${itemName} × ${item.quantity} — ${formatRupiah(item.price * item.quantity)}`);
  });
  getFieldValues().filter((field) => field.value).forEach((field) => addConfirmRow(field.label, field.value));
  addConfirmRow("Pembayaran", getPayment());
  addConfirmRow("Total", formatRupiah(cartTotal()));
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
        product_group_id: Number(orderPage.dataset.productGroupId),
        items: Array.from(cart.values()).map((item) => ({ variant_id: item.id, quantity: item.quantity })),
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
