import tkinter as tk
from tkinter import filedialog, messagebox, scrolledtext
from PIL import Image, ImageTk
import pytesseract
import os

try:
    from pdf2image import convert_from_path
    PDF_SUPPORT = True
except ImportError:
    PDF_SUPPORT = False

class OCRGuiApp:
    def __init__(self, root):
        self.root = root
        self.root.title("Fahrzeugschein OCR - Drag & Drop Edition")
        self.root.geometry("800x600")
        self.root.configure(bg="#24292f")
        self.file_path = None
        self.image_preview = None

        self.label = tk.Label(root, text="Zieh deinen Fahrzeugschein (PDF/JPG/PNG) hierher!", bg="#24292f", fg="#e1e4e8", font=("Arial", 16))
        self.label.pack(pady=20)

        self.drop_frame = tk.Frame(root, bg="#444c56", relief=tk.RIDGE, borderwidth=3, width=720, height=150)
        self.drop_frame.pack(pady=10)
        self.drop_frame.pack_propagate(0)
        self.drop_frame.bind("<Button-1>", self.select_file)
        self.label2 = tk.Label(self.drop_frame, text="Klick hier oder zieh eine Datei auf dieses Feld", bg="#444c56", fg="#e1e4e8")
        self.label2.pack(expand=True, fill="both")

        self.root.drop_target_register = getattr(self.root, "tkdnd", lambda *a, **kw: None)  # Dummy für Windows/Linux
        self.drop_frame.drop_target_register = getattr(self.drop_frame, "tkdnd", lambda *a, **kw: None)
        try:
            import tkinterdnd2
            self.dnd = tkinterdnd2.TkinterDnD.Tk()
            self.drop_frame.drop_target_register('DND_Files')
            self.drop_frame.dnd_bind('<<Drop>>', self.on_drop)
        except ImportError:
            # Kein Drag & Drop, aber Klick reicht auch
            pass

        self.image_label = tk.Label(root, bg="#24292f")
        self.image_label.pack(pady=10)

        self.ocr_btn = tk.Button(root, text="OCR starten", command=self.run_ocr, state="disabled", bg="#28a745", fg="white")
        self.ocr_btn.pack(pady=5)

        self.text_area = scrolledtext.ScrolledText(root, wrap=tk.WORD, width=90, height=14, font=("Consolas", 11), bg="#1e2227", fg="#e1e4e8")
        self.text_area.pack(pady=10)
        self.text_area.config(state="disabled")

        self.copy_btn = tk.Button(root, text="Text kopieren", command=self.copy_text, state="disabled", bg="#0366d6", fg="white")
        self.copy_btn.pack(pady=5)

    def select_file(self, event=None):
        filetypes = [("Bilder/PDF", "*.pdf *.jpg *.jpeg *.png"), ("Alle Dateien", "*.*")]
        filename = filedialog.askopenfilename(title="Datei auswählen", filetypes=filetypes)
        if filename:
            self.load_file(filename)

    def on_drop(self, event):
        filename = event.data.strip("{}")
        if os.path.isfile(filename):
            self.load_file(filename)

    def load_file(self, path):
        self.file_path = path
        ext = os.path.splitext(path)[1].lower()
        img = None
        if ext in [".jpg", ".jpeg", ".png"]:
            img = Image.open(path)
        elif ext == ".pdf" and PDF_SUPPORT:
            try:
                img = convert_from_path(path, first_page=1, last_page=1)[0]
            except Exception as e:
                messagebox.showerror("Fehler", f"PDF konnte nicht geladen werden: {e}")
                return
        else:
            messagebox.showwarning("Nicht unterstützt", "Nur PDF/JPG/PNG werden unterstützt.")
            return

        # Bild für Vorschau skalieren
        img_thumb = img.copy()
        img_thumb.thumbnail((500, 140))
        self.image_preview = ImageTk.PhotoImage(img_thumb)
        self.image_label.config(image=self.image_preview)
        self.ocr_btn.config(state="normal")
        self.text_area.config(state="normal")
        self.text_area.delete("1.0", tk.END)
        self.text_area.config(state="disabled")
        self.copy_btn.config(state="disabled")

    def run_ocr(self):
        if not self.file_path:
            return
        ext = os.path.splitext(self.file_path)[1].lower()
        images = []
        if ext in [".jpg", ".jpeg", ".png"]:
            images = [Image.open(self.file_path)]
        elif ext == ".pdf" and PDF_SUPPORT:
            images = convert_from_path(self.file_path)
        else:
            messagebox.showwarning("Nicht unterstützt", "Nur PDF/JPG/PNG werden unterstützt.")
            return

        result_text = ""
        for i, img in enumerate(images):
            result_text += pytesseract.image_to_string(img, lang="deu") + "\n"

        self.text_area.config(state="normal")
        self.text_area.delete("1.0", tk.END)
        self.text_area.insert(tk.END, result_text)
        self.text_area.config(state="disabled")
        self.copy_btn.config(state="normal")

    def copy_text(self):
        self.root.clipboard_clear()
        self.root.clipboard_append(self.text_area.get("1.0", tk.END))
        messagebox.showinfo("Kopiert!", "Text ist jetzt im Zwischenspeicher.")

if __name__ == "__main__":
    root = tk.Tk()
    app = OCRGuiApp(root)
    root.mainloop()
