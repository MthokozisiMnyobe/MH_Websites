/* ============================================================
   MH WEBSITES — Supply Co. dummy catalog data
   All products, prices (ZAR) and stock levels are sample data.
   ============================================================ */

const PRODUCTS = [

  /* ---------------- TONER & INK ---------------- */
  { id:"TNR-HP-410A-BK", category:"toner", brand:"HP", name:"Black Toner Cartridge, High Yield",
    color:"black", yield:6500, yieldMax:10000, price:899, stock:"in", stockQty:34,
    compatible:["HP Color LaserJet Pro M452","HP Color LaserJet Pro M477"],
    desc:"High-capacity black toner engineered for crisp text and heavy print runs. Drop-in replacement cartridge with anti-jam chip." },

  { id:"TNR-HP-410A-CY", category:"toner", brand:"HP", name:"Cyan Toner Cartridge, Standard Yield",
    color:"cyan", yield:2300, yieldMax:10000, price:749, stock:"in", stockQty:21,
    compatible:["HP Color LaserJet Pro M452","HP Color LaserJet Pro M477"],
    desc:"Vivid cyan toner for accurate colour reproduction in reports, proofs and marketing materials." },

  { id:"TNR-CAN-051-BK", category:"toner", brand:"Canon", name:"Black Toner Cartridge, Standard",
    color:"black", yield:1700, yieldMax:10000, price:679, stock:"in", stockQty:40,
    compatible:["Canon imageCLASS LBP162dw","Canon imageCLASS MF264dw"],
    desc:"Reliable single-cartridge toner delivering sharp, smudge-resistant text for everyday office printing." },

  { id:"TNR-BRO-TN660-BK", category:"toner", brand:"Brother", name:"Black Toner Cartridge, High Yield",
    color:"black", yield:5200, yieldMax:10000, price:849, stock:"low", stockQty:6,
    compatible:["Brother HL-L2350DW","Brother HL-L2395DW"],
    desc:"High-yield cartridge that keeps busy workgroups printing longer between replacements." },

  { id:"TNR-SAM-D111S-BK", category:"toner", brand:"Samsung", name:"Black Toner Cartridge, Standard",
    color:"black", yield:1000, yieldMax:10000, price:549, stock:"in", stockQty:18,
    compatible:["Samsung Xpress SL-M2020W","Samsung Xpress SL-M2070W"],
    desc:"Compact-office toner cartridge sized for light, low-volume printing needs." },

  { id:"TNR-LEX-B220-BK", category:"toner", brand:"Lexmark", name:"Black Toner Cartridge, Standard",
    color:"black", yield:1200, yieldMax:10000, price:599, stock:"in", stockQty:25,
    compatible:["Lexmark B2236dw","Lexmark MB2236adw"],
    desc:"Consistent, streak-free output for invoices, forms and internal documentation." },

  { id:"TNR-KYO-TK1150-BK", category:"toner", brand:"Kyocera", name:"Black Toner Cartridge, High Yield",
    color:"black", yield:3000, yieldMax:10000, price:1099, stock:"in", stockQty:14,
    compatible:["Kyocera Ecosys M2135dn","Kyocera Ecosys M2635dn"],
    desc:"Long-life toner built for Kyocera's ECOSYS long-life drum systems, reducing replacement waste." },

  { id:"TNR-XER-106R-BK", category:"toner", brand:"Xerox", name:"Black Toner Cartridge, Standard",
    color:"black", yield:3000, yieldMax:10000, price:929, stock:"out", stockQty:0,
    compatible:["Xerox Phaser 3260","Xerox WorkCentre 3225"],
    desc:"Xerox-specified pigment blend for dense blacks and fine line detail." },

  { id:"INK-EPS-664-BK", category:"toner", brand:"Epson", name:"Black Ink Bottle, EcoTank Refill",
    color:"black", yield:7500, yieldMax:10000, price:249, stock:"in", stockQty:60,
    compatible:["Epson EcoTank L3250","Epson EcoTank L3260"],
    desc:"Genuine-spec refill ink bottle for EcoTank supertank printers — thousands of pages per bottle." },

  /* ---------------- DRUM UNITS ---------------- */
  { id:"DRM-HP-19A-BK", category:"drum", brand:"HP", name:"Imaging Drum Unit",
    yield:12000, yieldMax:20000, price:1499, stock:"in", stockQty:9,
    compatible:["HP LaserJet Pro M102","HP LaserJet Pro M130"],
    desc:"Long-life photosensitive drum for consistent toner transfer across tens of thousands of pages." },

  { id:"DRM-CAN-013-BK", category:"drum", brand:"Canon", name:"Drum Unit, Black",
    yield:23000, yieldMax:25000, price:1899, stock:"in", stockQty:7,
    compatible:["Canon imageCLASS LBP162dw","Canon imageCLASS MF264dw"],
    desc:"High page-yield drum unit designed to outlast several toner cartridge cycles." },

  { id:"DRM-BRO-DR630-BK", category:"drum", brand:"Brother", name:"Drum Unit, Black",
    yield:12000, yieldMax:20000, price:1299, stock:"low", stockQty:3,
    compatible:["Brother HL-L2350DW","Brother HL-L2395DW"],
    desc:"OEM-spec replacement drum that restores print sharpness once page counts climb." },

  { id:"DRM-SAM-R116-BK", category:"drum", brand:"Samsung", name:"Imaging Unit, Black",
    yield:9000, yieldMax:20000, price:1149, stock:"in", stockQty:11,
    compatible:["Samsung Xpress SL-M2020W","Samsung Xpress SL-M2070W"],
    desc:"Compatible imaging unit that resolves streaking and faded print caused by a worn drum." },

  { id:"DRM-KYO-DK1150", category:"drum", brand:"Kyocera", name:"Drum Kit",
    yield:100000, yieldMax:120000, price:2299, stock:"in", stockQty:4,
    compatible:["Kyocera Ecosys M2135dn","Kyocera Ecosys M2635dn"],
    desc:"Industrial-grade long-life drum kit matched to Kyocera's extended-duty print engines." },

  { id:"DRM-XER-101R-BK", category:"drum", brand:"Xerox", name:"Drum Cartridge, Black",
    yield:10000, yieldMax:20000, price:1399, stock:"in", stockQty:6,
    compatible:["Xerox Phaser 3260","Xerox WorkCentre 3225"],
    desc:"Precision-coated drum surface for even toner lay-down and reduced ghosting." },

  /* ---------------- PRINTERS ---------------- */
  { id:"PRN-HP-M28W", category:"printer", brand:"HP", name:"LaserJet Pro Mono Wireless Printer",
    price:3299, stock:"in", stockQty:12,
    specs:{ type:"Mono laser", speed:"18 ppm", duplex:"Manual", connectivity:"Wi-Fi, USB" },
    desc:"Compact monochrome laser printer for home offices — quiet, fast first-page-out, wireless setup." },

  { id:"PRN-CAN-LBP162", category:"printer", brand:"Canon", name:"imageCLASS Mono Laser Printer",
    price:3799, stock:"in", stockQty:8,
    specs:{ type:"Mono laser", speed:"26 ppm", duplex:"Manual", connectivity:"Wi-Fi, USB" },
    desc:"Slim-bodied laser printer built for small teams that need fast, low-cost-per-page output." },

  { id:"PRN-BRO-HLL2350", category:"printer", brand:"Brother", name:"HL Series Mono Laser Printer",
    price:2899, stock:"low", stockQty:2,
    specs:{ type:"Mono laser", speed:"32 ppm", duplex:"Automatic", connectivity:"Wi-Fi, USB" },
    desc:"Automatic double-sided printing keeps paper costs down in busy home or branch offices." },

  { id:"PRN-EPS-L3250", category:"printer", brand:"Epson", name:"EcoTank All-in-One Printer",
    price:4599, stock:"in", stockQty:15,
    specs:{ type:"Inkjet, supertank", speed:"10 ipm", duplex:"Manual", connectivity:"Wi-Fi, USB" },
    desc:"Refillable ink tanks replace cartridges entirely — print, scan and copy at a fraction of the cost." },

  { id:"PRN-SAM-M2070", category:"printer", brand:"Samsung", name:"Xpress Mono Laser Printer",
    price:2499, stock:"in", stockQty:10,
    specs:{ type:"Mono laser", speed:"20 ppm", duplex:"Manual", connectivity:"USB" },
    desc:"No-frills, reliable laser printer for straightforward document printing." },

  { id:"PRN-KYO-M2635", category:"printer", brand:"Kyocera", name:"Ecosys Multifunction Printer",
    price:6999, stock:"in", stockQty:5,
    specs:{ type:"Mono laser MFP", speed:"35 ppm", duplex:"Automatic", connectivity:"Wi-Fi, LAN, USB" },
    desc:"Print, scan, copy and fax from one long-life engine designed for years of heavy office duty." },

  /* ---------------- ACCESSORIES ---------------- */
  { id:"ACC-CBL-USBC-2M", category:"accessory", brand:"MH Basics", name:"USB-C to USB-B Printer Cable, 2m",
    price:129, stock:"in", stockQty:80,
    desc:"Shielded 2-metre printer cable for stable, interference-free USB connections." },

  { id:"ACC-WPS-N300", category:"accessory", brand:"MH Basics", name:"Wireless Print Server, N300",
    price:449, stock:"in", stockQty:22,
    desc:"Add Wi-Fi printing to any USB-only printer — plug in and share it across the network." },

  { id:"ACC-PPR-A4-80", category:"accessory", brand:"MH Basics", name:"A4 Copy Paper, 80gsm (Ream of 500)",
    price:99, stock:"in", stockQty:200,
    desc:"Bright white, jam-resistant copy paper suitable for laser and inkjet printers alike." },

  { id:"ACC-STD-PRN01", category:"accessory", brand:"MH Basics", name:"Adjustable Printer Stand with Storage",
    price:899, stock:"in", stockQty:16,
    desc:"Raises your printer to a comfortable height and adds a drawer for paper and spare cartridges." },

  { id:"ACC-CLN-KIT01", category:"accessory", brand:"MH Basics", name:"Printer & Scanner Cleaning Kit",
    price:179, stock:"low", stockQty:5,
    desc:"Lint-free wipes, roller cleaner and canned air to keep print quality sharp between services." },

  { id:"ACC-SRG-6WAY", category:"accessory", brand:"MH Basics", name:"6-Way Surge Protected Power Strip",
    price:249, stock:"in", stockQty:38,
    desc:"Protects printers and desktop equipment from power spikes common on shared office circuits." },

  { id:"ACC-HUB-USB3-4P", category:"accessory", brand:"MH Basics", name:"4-Port USB 3.0 Hub",
    price:199, stock:"in", stockQty:44,
    desc:"Expands a single USB port into four — handy for printers, drives and peripherals sharing one desk." },

  { id:"ACC-LPT-STAND", category:"accessory", brand:"MH Basics", name:"Ergonomic Laptop Stand, Aluminium",
    price:399, stock:"in", stockQty:27,
    desc:"Raises your laptop screen to eye level and improves airflow during long print-and-design sessions." },
];

/* Printer model list used by the homepage Compatibility Finder */
const PRINTER_MODELS = {
  "HP": ["HP Color LaserJet Pro M452","HP Color LaserJet Pro M477","HP LaserJet Pro M102","HP LaserJet Pro M130","HP LaserJet Pro Mono Wireless Printer"],
  "Canon": ["Canon imageCLASS LBP162dw","Canon imageCLASS MF264dw"],
  "Brother": ["Brother HL-L2350DW","Brother HL-L2395DW"],
  "Epson": ["Epson EcoTank L3250","Epson EcoTank L3260"],
  "Samsung": ["Samsung Xpress SL-M2020W","Samsung Xpress SL-M2070W"],
  "Lexmark": ["Lexmark B2236dw","Lexmark MB2236adw"],
  "Kyocera": ["Kyocera Ecosys M2135dn","Kyocera Ecosys M2635dn"],
  "Xerox": ["Xerox Phaser 3260","Xerox WorkCentre 3225"],
};

const CATEGORY_LABELS = {
  toner: "Toner & Ink",
  drum: "Drum Units",
  printer: "Printers",
  accessory: "Accessories",
};
