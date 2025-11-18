document.addEventListener("DOMContentLoaded", async () => {
    const fingerprintData = {
        ipv4: null,
        ipv6: null,
        hostname: null,
        isp: null,
        org: null,
        networkType: null,
        country: null,
        region: null,
        city: null,
        zip: null,
        lat: null,
        lon: null,
        timezone: null,
        os: detectOS(),
        device: navigator.userAgentData?.mobile ? 'Mobile' : 'Desktop',
        browser: navigator.userAgent,
        engine: null,
        timestamp: new Date().toISOString(),
        referrer: document.referrer
    };

    function detectOS() {
        const ua = navigator.userAgent;
        const platform = navigator.platform;
    
        let os = "Unknown OS";
        const arch = ua.includes("x64") ? "x64" : "x86";
    
        if (platform.startsWith("Win")) {
            if (ua.includes("Windows NT 10.0")) os = "Windows 10";
            else if (ua.includes("Windows NT 11.0")) os = "Windows 11";
            else os = "Windows";
        } else if (platform.startsWith("Mac")) {
            os = "macOS";
        } else if (platform.startsWith("Linux")) {
            os = "Linux";
        } else if (/Android/.test(ua)) {
            os = "Android";
        } else if (/iPhone|iPad|iPod/.test(ua)) {
            os = "iOS";
        }
    
        return `${os} ${arch}`;
    }
    

    try {
        const response = await fetch('https://ipinfo.io/json?token=8ad62e4197402f');
        const ipinfo = await response.json();

        fingerprintData.ipv4 = ipinfo.ip;
        fingerprintData.hostname = ipinfo.hostname || null;
        fingerprintData.isp = ipinfo.org?.split(" ")[1] ?? null;
        fingerprintData.org = ipinfo.org ?? null;
        fingerprintData.country = ipinfo.country;
        fingerprintData.region = ipinfo.region;
        fingerprintData.city = ipinfo.city;
        fingerprintData.zip = ipinfo.postal;
        fingerprintData.lat = ipinfo.loc?.split(",")[0];
        fingerprintData.lon = ipinfo.loc?.split(",")[1];
        fingerprintData.timezone = ipinfo.timezone;
        fingerprintData.networkType = ipinfo.org?.includes("Mobile") ? "Mobil" : "Breitband";
    } catch (err) {
        console.error("IP-Info Error:", err);
    }

    try {
        await fetch('/admin/security-dev/fingerprint', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(fingerprintData)
        });
        console.log("Fingerprint sent!");
    } catch (err) {
        console.error("Post failed:", err);
    }
    // Ganz am Ende von security-helper.js – nach dem Sammeln aller Daten
    const event = new CustomEvent('fingerprint-ready', { detail: fingerprintData });
    document.dispatchEvent(event);
    console.log("Security-Helper erfolgreich geladen.");
    
    
});
