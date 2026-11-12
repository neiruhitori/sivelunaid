const backgroundMusic = new Audio(new URL("music/harvest-moon.mp3", document.body.dataset.welcomeAssets || "/Welcome/").href);
const musicControl = document.createElement("button");
const floatingControls = document.createElement("div");
const MUSIC_TIME_KEY = "veluna-music-time";
const MUSIC_TIME_SAVED_AT_KEY = "veluna-music-time-saved-at";
const MUSIC_PAUSED_KEY = "veluna-music-paused";
const backgroundMusicVolume = 0.18;

backgroundMusic.loop = true;
backgroundMusic.volume = backgroundMusicVolume;
backgroundMusic.preload = "auto";
backgroundMusic.setAttribute("aria-hidden", "true");
backgroundMusic.tabIndex = -1;
backgroundMusic.style.display = "none";
document.body.append(backgroundMusic);
floatingControls.className = "floating-controls";
floatingControls.setAttribute("aria-label", "Kontrol musik dan WhatsApp");
document.body.append(floatingControls);
musicControl.type = "button";
musicControl.className = "music-control";
musicControl.setAttribute("aria-label", "Putar musik latar");
musicControl.setAttribute("aria-pressed", "false");
musicControl.innerHTML = '<span class="music-control-icon" aria-hidden="true">♫</span><span class="music-control-label">Musik</span>';
floatingControls.append(musicControl);

const whatsappHeaderLink = document.querySelector(".header-actions .whatsapp-link");
let whatsappPlaceholder;

if (whatsappHeaderLink) {
  whatsappPlaceholder = document.createComment("WhatsApp header button position");
  whatsappHeaderLink.before(whatsappPlaceholder);
}

const updateFloatingWhatsapp = () => {
  if (!whatsappHeaderLink || !whatsappPlaceholder) return;

  if (window.scrollY > 120 && whatsappHeaderLink.parentElement !== floatingControls) {
    floatingControls.insertBefore(whatsappHeaderLink, musicControl);
    whatsappHeaderLink.classList.add("is-floating");
  } else if (window.scrollY <= 120 && whatsappHeaderLink.parentElement === floatingControls) {
    whatsappPlaceholder.replaceWith(whatsappHeaderLink);
    whatsappPlaceholder = document.createComment("WhatsApp header button position");
    whatsappHeaderLink.before(whatsappPlaceholder);
    whatsappHeaderLink.classList.remove("is-floating");
  }
};

window.addEventListener("scroll", updateFloatingWhatsapp, { passive: true });
updateFloatingWhatsapp();

// A pause is respected while moving between Welcome pages, but a full refresh
// starts the music again as requested.
const navigationEntry = performance.getEntriesByType("navigation")[0];
if (navigationEntry?.type === "reload") {
  sessionStorage.setItem(MUSIC_PAUSED_KEY, "false");
}

let musicWasPaused = sessionStorage.getItem(MUSIC_PAUSED_KEY) === "true";
let musicStarted = false;
let musicStartPromise = null;

const updateMusicControl = (isPlaying) => {
  musicControl.classList.toggle("is-playing", isPlaying);
  musicControl.setAttribute("aria-pressed", String(isPlaying));
  musicControl.setAttribute(
    "aria-label",
    isPlaying ? "Jeda musik latar" : "Putar musik latar",
  );
  musicControl.title = isPlaying ? "Jeda musik latar" : "Putar musik latar";
};

const saveMusicPosition = () => {
  if (!Number.isFinite(backgroundMusic.currentTime)) return;
  sessionStorage.setItem(MUSIC_TIME_KEY, String(backgroundMusic.currentTime));
  sessionStorage.setItem(MUSIC_TIME_SAVED_AT_KEY, String(Date.now()));
};

const waitForMusicMetadata = () => {
  if (backgroundMusic.readyState >= HTMLMediaElement.HAVE_METADATA) return Promise.resolve();
  return new Promise((resolve) => {
    backgroundMusic.addEventListener("loadedmetadata", resolve, { once: true });
    backgroundMusic.load();
  });
};

const restoreSavedMusicPosition = async () => {
  await waitForMusicMetadata();
  const savedTime = Number(sessionStorage.getItem(MUSIC_TIME_KEY));
  const savedAt = Number(sessionStorage.getItem(MUSIC_TIME_SAVED_AT_KEY));
  if (!Number.isFinite(savedTime) || savedTime <= 0 || !Number.isFinite(backgroundMusic.duration)) return;

  const elapsed = Number.isFinite(savedAt) ? Math.max(0, (Date.now() - savedAt) / 1000) : 0;
  const targetTime = (savedTime + elapsed) % backgroundMusic.duration;
  if (Math.abs(backgroundMusic.currentTime - targetTime) < 0.25) return;

  await new Promise((resolve) => {
    const finishSeek = () => {
      clearTimeout(timeoutId);
      resolve();
    };
    const timeoutId = setTimeout(finishSeek, 1200);
    backgroundMusic.addEventListener("seeked", finishSeek, { once: true });
    backgroundMusic.currentTime = targetTime;
  });
};

const startBackgroundMusic = () => {
  if (musicStarted || musicWasPaused) return Promise.resolve();
  if (musicStartPromise) return musicStartPromise;

  musicStartPromise = restoreSavedMusicPosition().then(() => backgroundMusic.play()).then(() => {
    musicStarted = true;
    musicWasPaused = false;
    sessionStorage.setItem(MUSIC_PAUSED_KEY, "false");
    updateMusicControl(true);
    document.removeEventListener("pointerdown", resumeOnFirstInteraction);
    document.removeEventListener("keydown", resumeOnFirstInteraction);
  }).catch((error) => {
    if (error.name === "NotAllowedError") {
      updateMusicControl(false);
    } else {
      musicControl.setAttribute("aria-label", "Musik latar gagal dimuat");
      musicControl.title = "Musik latar gagal dimuat";
      console.error("Musik latar Veluna gagal diputar.", error);
    }
  }).finally(() => {
    musicStartPromise = null;
  });
  return musicStartPromise;
};

const resumeOnFirstInteraction = () => {
  if (musicWasPaused) return;
  startBackgroundMusic();
  if (!backgroundMusic.paused) {
    document.removeEventListener("pointerdown", resumeOnFirstInteraction);
    document.removeEventListener("keydown", resumeOnFirstInteraction);
  }
};

backgroundMusic.addEventListener("play", () => updateMusicControl(true));
backgroundMusic.addEventListener("pause", () => updateMusicControl(false));
backgroundMusic.addEventListener("timeupdate", () => {
  if (!backgroundMusic.paused && Number.isFinite(backgroundMusic.currentTime)) {
    saveMusicPosition();
  }
});

backgroundMusic.addEventListener("pause", saveMusicPosition);

musicControl.addEventListener("click", () => {
  if (backgroundMusic.paused) {
    musicWasPaused = false;
    sessionStorage.setItem(MUSIC_PAUSED_KEY, "false");
    backgroundMusic.play().then(() => {
      musicStarted = true;
      updateMusicControl(true);
      document.removeEventListener("pointerdown", resumeOnFirstInteraction);
      document.removeEventListener("keydown", resumeOnFirstInteraction);
    }).catch((error) => {
      if (error.name === "NotAllowedError") {
        updateMusicControl(false);
        return;
      }

      musicControl.setAttribute("aria-label", "Musik latar gagal dimuat");
      musicControl.title = "Musik latar gagal dimuat";
      console.error("Musik latar Veluna gagal diputar.", error);
    });
  } else {
    musicWasPaused = true;
    sessionStorage.setItem(MUSIC_PAUSED_KEY, "true");
    sessionStorage.setItem(MUSIC_TIME_KEY, String(backgroundMusic.currentTime));
    backgroundMusic.pause();
  }
});

window.addEventListener("pagehide", () => {
  saveMusicPosition();
});
window.addEventListener("beforeunload", saveMusicPosition);
document.addEventListener("click", (event) => {
  const link = event.target.closest("a[href]");
  if (!link || link.target || link.hasAttribute("download")) return;

  try {
    const destination = new URL(link.href, window.location.href);
    if (destination.origin === window.location.origin && destination.href !== window.location.href) {
      saveMusicPosition();
    }
  } catch {
    // Ignore malformed or non-navigation links.
  }
}, true);

window.addEventListener("pageshow", (event) => {
  musicWasPaused = sessionStorage.getItem(MUSIC_PAUSED_KEY) === "true";
  if (event.persisted && !musicWasPaused && backgroundMusic.paused) {
    backgroundMusic.play().then(() => {
      musicStarted = true;
      updateMusicControl(true);
    }).catch(() => {
      musicStarted = false;
      startBackgroundMusic();
    });
  }
});

document.addEventListener("pointerdown", resumeOnFirstInteraction);
document.addEventListener("keydown", resumeOnFirstInteraction);
startBackgroundMusic();
