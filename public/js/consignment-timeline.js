window.ConsignmentTimeline = (function () {
    function fmt(date) {
        if (!date || date === "1970-01-01" || date === "0000-00-00") return "—";
        const d = new Date(date);
        return d.toLocaleDateString("en-GB", {
            day: "2-digit",
            month: "short",
            year: "numeric",
        });
    }

    function fmtNum(val) {
        return parseFloat(val || 0).toLocaleString("en-GH", {
            minimumFractionDigits: 2,
        });
    }

    function statusLabel(s) {
        const map = {
            0: { label: "Cleared", bg: "#f3f4f6", color: "#374151" },
            1: { label: "Not Arrived", bg: "#fef3c7", color: "#92400e" },
            2: { label: "Pending", bg: "#dbeafe", color: "#1e40af" },
            3: { label: "Gated Out", bg: "#dcfce7", color: "#166534" },
        };
        return map[s] ?? { label: "Unknown", bg: "#f3f4f6", color: "#6b7280" };
    }

    function ageCls(days) {
        if (days <= 7) return "#15803d";
        if (days <= 14) return "#b45309";
        if (days <= 30) return "#c2410c";
        return "#b91c1c";
    }

    function renderTimeline(stages, targetId) {
        const wrap = document.getElementById(targetId);
        if (!wrap) return;

        wrap.innerHTML = stages
            .map(function (s, i) {
                const isLast = i === stages.length - 1;
                const dotBg = s.done ? "#15803d" : "#e5e7eb";
                const dotColor = s.done ? "#fff" : "#9ca3af";
                const dotIcon = s.done ? "✓" : i + 1;
                const labelCls = s.done ? "#15803d" : "#9ca3af";
                const connector = !isLast
                    ? '<div style="position:absolute; top:21px; left:50%; right:-50%; height:3px; background:' +
                      (s.done ? "#15803d" : "#e5e7eb") +
                      '; z-index:0;"></div>'
                    : "";

                return (
                    '<div style="flex:1; display:flex; flex-direction:column; align-items:center; position:relative; z-index:1;">' +
                    connector +
                    '<div style="width:42px; height:42px; border-radius:50%; background:' +
                    dotBg +
                    "; " +
                    "color:" +
                    dotColor +
                    "; display:flex; align-items:center; justify-content:center; " +
                    'font-size:12px; font-weight:700; position:relative; z-index:1;">' +
                    dotIcon +
                    "</div>" +
                    '<p style="font-size:10px; font-weight:700; color:' +
                    labelCls +
                    '; margin-top:8px; text-align:center; max-width:80px;">' +
                    s.stage +
                    "</p>" +
                    '<p style="font-size:9px; color:#6b7280; margin-top:3px; text-align:center;">' +
                    fmt(s.date) +
                    "</p>" +
                    "</div>"
                );
            })
            .join("");
    }

    function renderContainers(containers, targetId) {
        const tbody = document.getElementById(targetId);
        if (!tbody) return;

        if (!containers || containers.length === 0) {
            tbody.innerHTML =
                '<tr><td colspan="6" style="text-align:center; padding:1rem; color:#9ca3af; font-size:12px;">No container details found.</td></tr>';
            return;
        }

        tbody.innerHTML = containers
            .map(function (c, i) {
                const gateOut = fmt(c.GateOutDate);
                const ret = fmt(c.ReturnDate);

                let demurrage = "—";
                if (c.GateOutDate && c.GateOutDate !== "0000-00-00") {
                    const endDate =
                        c.ReturnDate && c.ReturnDate !== "0000-00-00"
                            ? new Date(c.ReturnDate)
                            : new Date();
                    const startDate = new Date(c.GateOutDate);
                    const days = Math.floor((endDate - startDate) / 86400000);
                    demurrage =
                        days > 0
                            ? '<span style="color:' +
                              ageCls(days) +
                              '; font-weight:700;">' +
                              days +
                              " days</span>"
                            : "0 days";
                }

                return (
                    '<tr style="' +
                    (i % 2 === 0 ? "" : "background:#f9fafb;") +
                    '">' +
                    '<td style="padding:7px 10px; font-family:monospace; font-size:12px;">' +
                    (c.ContainerNo || "—") +
                    "</td>" +
                    '<td style="padding:7px 10px; font-size:12px;">' +
                    (c.ContainerSize || "—") +
                    "</td>" +
                    '<td style="padding:7px 10px; font-size:12px;">' +
                    (c.Weight || "—") +
                    "</td>" +
                    '<td style="padding:7px 10px; font-size:12px;">' +
                    gateOut +
                    "</td>" +
                    '<td style="padding:7px 10px; font-size:12px;">' +
                    ret +
                    "</td>" +
                    '<td style="padding:7px 10px; font-size:12px;">' +
                    demurrage +
                    "</td>" +
                    "</tr>"
                );
            })
            .join("");
    }

    function renderManifest(manifest, targetTableId, targetEmptyId) {
        const empty = document.getElementById(targetEmptyId);
        const table = document.getElementById(targetTableId);
        const tbody = table ? table.querySelector("tbody") : null;
        if (!empty || !table || !tbody) return;

        if (!manifest || manifest.length === 0) {
            empty.style.display = "block";
            table.style.display = "none";
            return;
        }

        empty.style.display = "none";
        table.style.display = "table";

        tbody.innerHTML = manifest
            .map(function (m, i) {
                const tel = m.ConsigneeTel || "";
                const callBtn = tel
                    ? '<a href="tel:' +
                      tel +
                      '" style="display:inline-block; padding:3px 8px; background:#185FA5; color:#fff; border-radius:4px; font-size:9px; font-weight:700; text-decoration:none; margin-right:4px;">📞 Call</a>'
                    : "";
                const smsBtn = tel
                    ? '<a href="sms:' +
                      tel +
                      '" style="display:inline-block; padding:3px 8px; background:#15803d; color:#fff; border-radius:4px; font-size:9px; font-weight:700; text-decoration:none;">💬 SMS</a>'
                    : "";

                return (
                    '<tr style="' +
                    (i % 2 === 0 ? "" : "background:#f9fafb;") +
                    '">' +
                    '<td style="padding:7px 10px; font-family:monospace; font-size:12px;">' +
                    (m.HouseBL || "—") +
                    "</td>" +
                    '<td style="padding:7px 10px; font-size:12px; font-weight:600;">' +
                    (m.ConsigneeName || "—") +
                    "</td>" +
                    '<td style="padding:7px 10px; font-size:12px; color:#6b7280;">' +
                    (tel || "—") +
                    "</td>" +
                    '<td style="padding:7px 10px; font-size:12px;">' +
                    (m.Description || "—") +
                    "</td>" +
                    '<td style="padding:7px 10px; font-size:12px;">' +
                    (m.Weight || "—") +
                    "</td>" +
                    '<td style="padding:7px 10px; font-size:12px;">' +
                    (m.Package || "—") +
                    " " +
                    (m.Unit || "") +
                    "</td>" +
                    '<td style="padding:7px 10px;">' +
                    callBtn +
                    smsBtn +
                    "</td>" +
                    "</tr>"
                );
            })
            .join("");
    }

    function renderPayments(
        payments,
        totalExp,
        totalRev,
        targetTableId,
        targetEmptyId,
        targetTfootId,
    ) {
        const empty = document.getElementById(targetEmptyId);
        const table = document.getElementById(targetTableId);
        const tbody = table ? table.querySelector("tbody") : null;
        const tfoot = document.getElementById(targetTfootId);
        if (!empty || !table || !tbody || !tfoot) return;

        if (!payments || payments.length === 0) {
            empty.style.display = "block";
            table.style.display = "none";
            return;
        }

        empty.style.display = "none";
        table.style.display = "table";

        tbody.innerHTML = payments
            .map(function (p, i) {
                return (
                    '<tr style="' +
                    (i % 2 === 0 ? "" : "background:#f9fafb;") +
                    '">' +
                    '<td style="padding:7px 10px; font-size:12px;">' +
                    fmt(p.Date) +
                    "</td>" +
                    '<td style="padding:7px 10px; font-family:monospace; font-size:12px;">' +
                    (p.ReceiptNo || "—") +
                    "</td>" +
                    '<td style="padding:7px 10px; font-family:monospace; font-size:12px;">' +
                    (p.HBL || "—") +
                    "</td>" +
                    '<td style="padding:7px 10px; font-size:12px;">' +
                    (p.AccountName || "—") +
                    "</td>" +
                    '<td style="padding:7px 10px; font-size:12px; text-align:right; color:#b91c1c;">' +
                    fmtNum(p.Expenditure) +
                    "</td>" +
                    '<td style="padding:7px 10px; font-size:12px; text-align:right; color:#15803d;">' +
                    fmtNum(p.Revenue) +
                    "</td>" +
                    "</tr>"
                );
            })
            .join("");

        tfoot.innerHTML =
            '<tr style="background:#f3f4f6; border-top:2px solid #185FA5;">' +
            '<td colspan="4" style="padding:8px 10px; font-size:12px; font-weight:700; text-align:right;">Totals</td>' +
            '<td style="padding:8px 10px; font-size:12px; font-weight:700; text-align:right; color:#b91c1c;">GH₵ ' +
            fmtNum(totalExp) +
            "</td>" +
            '<td style="padding:8px 10px; font-size:12px; font-weight:700; text-align:right; color:#15803d;">GH₵ ' +
            fmtNum(totalRev) +
            "</td>" +
            "</tr>";
    }

    return {
        fmt,
        fmtNum,
        statusLabel,
        ageCls,
        renderTimeline,
        renderContainers,
        renderManifest,
        renderPayments,
    };
})();
