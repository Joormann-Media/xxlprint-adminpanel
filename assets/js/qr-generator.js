// Globale Variablen
let qrCode = null;
let contactPhotoBase64 = "";
let logoImageDataUrl = "";

// Farb-Swatches initialisieren
const colorInput   = document.getElementById("color");
const bgInput      = document.getElementById("bgcolor");
const colorSwatch  = document.getElementById("color-swatch");
const bgSwatch     = document.getElementById("bgcolor-swatch");
if (colorSwatch) {
  colorSwatch.style.backgroundColor = colorInput.value;
}
if (bgSwatch) {
  bgSwatch.style.backgroundColor = bgInput.value;
}
colorInput.addEventListener("input", e => {
  if (colorSwatch) {
    colorSwatch.style.backgroundColor = e.target.value;
  }
  updateQRCode();
});
bgInput.addEventListener("input", e => {
  if (bgSwatch) {
    bgSwatch.style.backgroundColor = e.target.value;
  }
  updateQRCode();
});

// Lange Base64-Strings umbrechen
function foldBase64(base64) {
  return base64.match(/.{1,76}/g).join("\n ");
}

// 1) vCard-String erzeugen
function generateVCard(data) {
  const { name, phone, email, address, city, state, zipcode,
          country, company, website, birthday, photoBase64 } = data;
  let vcard = `BEGIN:VCARD
VERSION:3.0
N:${name}
FN:${name}
TEL;TYPE=CELL,VOICE:${phone}
EMAIL;TYPE=INTERNET,HOME:${email}
`;
  if (address||city||state||zipcode||country) {
    vcard += `ADR;TYPE=HOME:;;${address};${city};${state};${zipcode};${country}\n`;
  }
  if (company) vcard += `ORG:${company}\n`;
  if (website) vcard += `URL:${website}\n`;
  if (birthday) {
    const b = birthday.replace(/-/g, "");
    vcard += `BDAY:${b}\n`;
  }
  if (photoBase64) {
    const mime = photoBase64.startsWith("data:image/png") ? "PNG" : "JPEG";
    const content = photoBase64.split(",")[1];
    vcard += `PHOTO;ENCODING=b;TYPE=${mime}:` + foldBase64(content) + "\n";
  }
  vcard += "END:VCARD";
  return vcard;
}

// 2) WLAN-String erzeugen
function generateWifiString({ ssid, password, encryption, hidden }) {
  const parts = [
    `T:${encryption||"nopass"}`,
    `S:${ssid||""}`,
    `P:${password||""}`
  ];
  if (hidden) parts.push("H:true");
  return "WIFI:" + parts.join(";") + ";;";
}

// 3) Freitext-String
function generateTextString(text) {
  return text || "";
}

// 4) Termin-String im ICS-Format
function generateAppointmentString({ date, time, location, description }) {
  const dt = (date || "").replace(/-/g, "") + "T" + (time || "").replace(/:/g, "") + "00";
  if (!date && !time) return ""; // Keine Vorschau, wenn weder Datum noch Zeit angegeben sind
  return `BEGIN:VCALENDAR
VERSION:2.0
BEGIN:VEVENT
DTSTART:${dt}
DTEND:${dt}
SUMMARY:Termin
LOCATION:${location || ""}
DESCRIPTION:${description || ""}
END:VEVENT
END:VCALENDAR`;
}

/**
 * Erzeugt einen EPC-/SEPA-QR-String (SEPA Credit Transfer).
 * 1. Zeile:  Service-Tag „BCD“  
 * 2. Zeile:  Version „001“  
 * 3. Zeile:  Zeichensatz „1“ (UTF-8)  
 * 4. Zeile:  Payment-Network „SCT“  
 * 5. Zeile:  BIC  
 * 6. Zeile:  Name des Empfängers  
 * 7. Zeile:  IBAN (ohne Leerzeichen)  
 * 8. Zeile:  Betrag in EUR (z. B. „EUR50.00“) – leer, wenn kein Betrag  
 * 9. Zeile:  Unstrukturierter Verwendungszweck  
 * 10.Zeile: Leere Abschlusszeile
 */
function generateIbanString({ iban, bic, accountHolder, amount, purpose }) {
  // 1) IBAN säubern
  const cleanIban = iban.replace(/\s+/g, '').toUpperCase();

  // 2) Betrag-Zeile nur befüllen, wenn ein Betrag eingegeben wurde
  const amountLine = amount
    ? 'EUR' + parseFloat(amount).toFixed(2)
    : '';

  // 3) Alle EPC-Zeilen zusammenbauen
  const lines = [
    'BCD',                 // Service-Tag
    '001',                 // Version
    '1',                   // UTF-8
    'SCT',                 // SEPA Credit Transfer
    bic || '',             // BIC (optional)
    accountHolder || '',   // Name Empfänger
    cleanIban,             // IBAN
    amountLine,            // Betrag
    purpose || '',         // === hier muss dein Verwendungszweck stehen ===
    ''                     // Abschluss-Leerzeile
  ];

  return lines.join('\n');
}

// 6) SMS-String
function generateSmsString({ number, message }) {
  // Gängiges Format: SMSTO:<Nummer>:<Nachricht>
  return `SMSTO:${number}:${message}`;
}

// 7) URL-String
function generateUrlString(url) {
  return url || "";
}

// QR-Code-Instanz erzeugen
function createQRCode(config) {
  const instance = new QRCodeStyling(config);
  const container = document.getElementById("qrcode");
  container.innerHTML = "";
  instance.append(container);
  return instance;
}

// Wert aus Input/Select/Textarea holen
function getValue(id) {
  const el = document.getElementById(id);
  if (!el) {
    console.warn(`Element with id "${id}" not found.`);
    return ""; // Return an empty string if the element is not found
  }
  return el.value.trim();
}

// Hauptfunktion: entscheidet vCard / WLAN / Termin
function updateQRCode() {
  const form = document.getElementById("vcard-form");
  if (!form.checkValidity()) return;

  // vCard-Daten
  const vcardData = {
    name: getValue("name"),
    phone: getValue("phone"),
    email: getValue("email"),
    address: getValue("address"),
    city: getValue("city"),
    state: getValue("state"),
    zipcode: getValue("zipcode"),
    country: getValue("country"),
    company: getValue("company"),
    website: getValue("website"),
    birthday: getValue("birthday"),
    photoBase64: contactPhotoBase64
  };

  // WLAN-Daten
  const wifiData = {
    ssid: getValue("wifi-ssid"),
    password: getValue("wifi-password"),
    encryption: getValue("wifi-encryption"),
    hidden: document.getElementById("wifi-hidden")?.checked || false
  };
// Freitext
  const textData = getValue("text-content");
  // Termin-Daten
  const appointmentData = {
    date: getValue("appointment-date"),
    time: getValue("appointment-time"),
    location: getValue("appointment-location"),
    description: getValue("appointment-description")
  };
// IBAN/BIC-Daten (angepasst auf neue ID)
  const ibanData = {
    iban:          getValue("iban-input"),    // <— hier auf „iban-input“ statt „iban“
    bic:           getValue("bic"),
    accountHolder: getValue("account-holder"),
    bankName:      getValue("bank-name"),
    amount:        getValue("amount"),
    purpose:       getValue("purpose")
  };
  // Design-Einstellungen
  const imageSize        = parseFloat(getValue("image-size")) || 0.4;
  const hideBgDots       = document.getElementById("hide-bg-dots").checked;
  const margin           = parseInt(getValue("image-margin"), 10) || 0;
  const crossOrigin      = getValue("cross-origin") || "anonymous";
  const errorCorrection  = getValue("ec-level");

  // Welcher Tab ist aktiv?
  const activePane = document.querySelector("#qrTabContent .tab-pane.show.active").id;
  let qrData = "";
  if (activePane === "wifi") {
    qrData = generateWifiString(wifiData);
  } else if (activePane === "appointment") {
    qrData = generateAppointmentString(appointmentData);
  }
  else if (activePane === "iban") {
    qrData = generateIbanString(ibanData);
  
  } else if (activePane === "sms") {
    qrData = generateSmsString({
      number: getValue("sms-number"),
      message: getValue("sms-message")
    });
  } else if (activePane === "text") {
    qrData = generateTextString(textData);
  } else if (activePane === "url") {
    const urlData = getValue("url-input"); // from the actual text input
    qrData = generateUrlString(urlData);
  } else {
    qrData = generateVCard(vcardData);
  }

  // QR-Code-Konfiguration
  const options = {
    width: 450,
    height: 450,
    type: "svg",
    data: qrData,
    image: logoImageDataUrl || "",
    imageOptions: {
      crossOrigin,
      margin,
      hideBackgroundDots: hideBgDots,
      imageSize
    },
    qrOptions: {
      errorCorrectionLevel: errorCorrection
    },
    dotsOptions: {
      color: getValue("color"),
      type: getValue("dot-type"),
      gradient: {
        type: getValue("gradient-type"),
        rotation: parseInt(getValue("gradient-rotation"), 10) || 0,
        colorStops: [
          { offset: 0, color: getValue("dot-gradient-color-1") },
          { offset: 1, color: getValue("dot-gradient-color-2") }
        ]
      }
    },
    backgroundOptions: {
      color: getValue("bgcolor"),
      gradient: {
        type: getValue("background-gradient-type"),
        rotation: parseInt(getValue("background-gradient-rotation"), 10) || 0,
        colorStops: [
          { offset: 0, color: getValue("background-gradient-color-1") },
          { offset: 1, color: getValue("background-gradient-color-2") }
        ]
      }
    },
    cornersSquareOptions: {
      type: getValue("corner-type-outside"),
      color: getValue("color"),
      gradient: {
        type: getValue("corner-gradient-type-outside"),
        rotation: parseInt(getValue("corner-gradient-rotation"), 10) || 0,
        colorStops: [
          { offset: 0, color: getValue("corner-gradient-color-1") },
          { offset: 1, color: getValue("corner-gradient-color-2") }
        ]
      }
    },
    cornersDotOptions: {
      type: getValue("corner-type-inside"),
      color: getValue("color"),
      gradient: {
        type: getValue("corner-gradient-type-inside"),
        rotation: parseInt(getValue("corner-gradient-rotation"), 10) || 0,
        colorStops: [
          { offset: 0, color: getValue("corner-gradient-color-1") },
          { offset: 1, color: getValue("corner-gradient-color-2") }
        ]
      }
    }
  };

  // QR-Code rendern
  qrCode = createQRCode(options);
}

// Event-Listener für Inputs, Selects und Textareas
document.querySelectorAll("#vcard-form input, #vcard-form select, #vcard-form textarea")
  .forEach(el => el.addEventListener("input", updateQRCode));

// Hinzufügen von Event-Listenern für IBAN/BIC Felder
document.querySelectorAll("#iban, #bic, #account-holder, #bank-name, #amount, #purpose")
  .forEach(el => el.addEventListener("input", updateQRCode));

// Hinzufügen von Event-Listenern für URL-Feld
document.getElementById("url-input").addEventListener("input", updateQRCode);


// Logo laden
document.getElementById("logo").addEventListener("change", e => {
  const file = e.target.files[0];
  if (!file) { logoImageDataUrl = ""; updateQRCode(); return; }
  const reader = new FileReader();
  reader.onload = ev => { logoImageDataUrl = ev.target.result; updateQRCode(); };
  reader.readAsDataURL(file);
});

// Kontaktfoto laden
document.getElementById("contact-photo").addEventListener("change", e => {
  const file = e.target.files[0];
  if (!file) return;
  const reader = new FileReader();
  reader.onload = ev => { contactPhotoBase64 = ev.target.result; updateQRCode(); };
  reader.readAsDataURL(file);
});

// Download-Buttons
document.getElementById("download-png").addEventListener("click", () => {
  qrCode?.download({ extension: "png" });
});
document.getElementById("download-svg").addEventListener("click", () => {
  qrCode?.download({ extension: "svg" });
});
document.getElementById("download-pdf").addEventListener("click", () => {
  const svgEl = document.querySelector("#qrcode svg");
  if (!svgEl) {
    alert("QR-Code nicht gefunden.");
    return;
  }

  const svgData = new XMLSerializer().serializeToString(svgEl);
  const img = new Image();

  img.onload = () => {
    // 👇 Eingabe: gewünschte Druckbreite in CM und Ziel-DPI
    const widthCM = 5;
    const dpi = 300;
    const widthInch = widthCM / 2.54;
    const widthPx = Math.round(widthInch * dpi);
    const aspectRatio = img.height / img.width;
    const heightPx = Math.round(widthPx * aspectRatio);

    // 🔍 Canvas vorbereiten mit echter DPI-Auflösung
    const canvas = document.createElement("canvas");
    canvas.width = widthPx;
    canvas.height = heightPx;
    const ctx = canvas.getContext("2d");
    ctx.drawImage(img, 0, 0, widthPx, heightPx);

    const { jsPDF } = window.jspdf;
    const pdf = new jsPDF({
      orientation: widthPx > heightPx ? "landscape" : "portrait",
      unit: "mm",
      format: "a4"
    });

    const imgData = canvas.toDataURL("image/png");

    // PDF-Positionierung in MM
    const pdfWidthMM = widthCM;
    const pdfHeightMM = widthCM * aspectRatio;
    const pdfPageWidth = 210; // A4
    const x = (pdfPageWidth - pdfWidthMM) / 2;

    pdf.addImage(imgData, "PNG", x, 40, pdfWidthMM, pdfHeightMM);
    pdf.save("qrcode-300dpi.pdf");
  };

  // 🧠 Wichtig: Unicode-safe Base64-Encoding für SVG
  const svgBase64 = btoa(unescape(encodeURIComponent(svgData)));
  img.src = "data:image/svg+xml;base64," + svgBase64;
});



// Dark Mode umschalten
document.getElementById("toggle-dark-mode")
  .addEventListener("click", () => {
    document.body.classList.toggle("dark-mode");
  });

// Initiales Rendering
updateQRCode();