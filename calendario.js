document.addEventListener("DOMContentLoaded", function () {
  const MESES = [
    "enero",
    "febrero",
    "marzo",
    "abril",
    "mayo",
    "junio",
    "julio",
    "agosto",
    "septiembre",
    "octubre",
    "noviembre",
    "diciembre",
  ];
  const DIAS = ["DO", "LU", "MA", "MI", "JU", "VI", "SA"];

  let selIngreso = null;
  let selSalida = null;
  let viewI = { y: new Date().getFullYear(), m: new Date().getMonth() };
  let viewS = { ...viewI };

  function buildCalendar(containerId, viewObj, onSelect, minDate) {
    const wrap = document.getElementById(containerId);
    const today = new Date();
    today.setHours(0, 0, 0, 0);
    wrap.innerHTML = "";

    const header = document.createElement("div");
    header.className = "cal-header";
    header.innerHTML = `
      <span class="cal-month-label">
        ${MESES[viewObj.m]} de ${viewObj.y} ▾
      </span>
      <div class="cal-nav">
        <button class="prev-btn">↑</button>
        <button class="next-btn">↓</button>
      </div>`;
    wrap.appendChild(header);

    header.querySelector(".prev-btn").onclick = (e) => {
      e.stopPropagation();
      viewObj.m--;
      if (viewObj.m < 0) {
        viewObj.m = 11;
        viewObj.y--;
      }
      buildCalendar(containerId, viewObj, onSelect, minDate);
    };
    header.querySelector(".next-btn").onclick = (e) => {
      e.stopPropagation();
      viewObj.m++;
      if (viewObj.m > 11) {
        viewObj.m = 0;
        viewObj.y++;
      }
      buildCalendar(containerId, viewObj, onSelect, minDate);
    };

    const grid = document.createElement("div");
    grid.className = "cal-grid";

    DIAS.forEach((d) => {
      const wd = document.createElement("div");
      wd.className = "cal-wd";
      wd.textContent = d;
      grid.appendChild(wd);
    });

    const firstDay = new Date(viewObj.y, viewObj.m, 1).getDay();
    const daysInMonth = new Date(viewObj.y, viewObj.m + 1, 0).getDate();

    let min = today;
    if (minDate) {
      min = new Date(minDate);
      min.setHours(0, 0, 0, 0);
    }

    for (let i = 0; i < firstDay; i++) {
      const empty = document.createElement("div");
      empty.className = "cal-day empty";
      grid.appendChild(empty);
    }

    for (let d = 1; d <= daysInMonth; d++) {
      const date = new Date(viewObj.y, viewObj.m, d);
      date.setHours(0, 0, 0, 0);

      const isPast = date < min;
      const isToday = date.getTime() === today.getTime();

      const el = document.createElement("div");
      el.className = ["cal-day", isPast ? "past" : "", isToday ? "today" : ""]
        .join(" ")
        .trim();
      el.textContent = d;

      if (!isPast) {
        el.addEventListener("click", (e) => {
          e.stopPropagation();
          onSelect(date);
        });
      }
      grid.appendChild(el);
    }
    wrap.appendChild(grid);
  }

  function fmt(date) {
    if (!date) return null;
    const d = String(date.getDate()).padStart(2, "0");
    const m = String(date.getMonth() + 1).padStart(2, "0");
    return `${d}/${m}/${date.getFullYear()}`;
  }

  function fmtISO(date) {
    if (!date) return "";
    const d = String(date.getDate()).padStart(2, "0");
    const m = String(date.getMonth() + 1).padStart(2, "0");
    return `${date.getFullYear()}-${m}-${d}`;
  }

  function openCal(which) {
    document
      .querySelectorAll(".cal-popup")
      .forEach((p) => p.classList.remove("open"));

    if (which === "ingreso") {
      buildCalendar("cal-ingreso", viewI, selectIngreso, null);
      document.getElementById("cal-ingreso").classList.add("open");
    } else {
      let minSalida = null;
      if (selIngreso) {
        const siguiente = new Date(selIngreso);
        siguiente.setDate(siguiente.getDate() + 1);
        minSalida = fmtISO(siguiente);
      }
      buildCalendar("cal-salida", viewS, selectSalida, minSalida);
      document.getElementById("cal-salida").classList.add("open");
    }
  }

  function selectIngreso(date) {
    selIngreso = date;
    document.getElementById("fecha_ingreso").value = fmtISO(date) || "";

    const disp = document.getElementById("display-ingreso");
    const txt = document.getElementById("txt-ingreso");
    if (date) {
      disp.classList.remove("empty");
      txt.textContent = fmt(date);
    } else {
      disp.classList.add("empty");
      txt.textContent = "dd/mm/aaaa";
    }

    if (selSalida && selSalida <= selIngreso) {
      selSalida = null;
      document.getElementById("fecha_salida").value = "";
      document.getElementById("display-salida").classList.add("empty");
      document.getElementById("txt-salida").textContent = "dd/mm/aaaa";
    }

    document.getElementById("cal-ingreso").classList.remove("open");
  }

  function selectSalida(date) {
    selSalida = date;
    document.getElementById("fecha_salida").value = fmtISO(date) || "";

    const disp = document.getElementById("display-salida");
    const txt = document.getElementById("txt-salida");
    if (date) {
      disp.classList.remove("empty");
      txt.textContent = fmt(date);
    } else {
      disp.classList.add("empty");
      txt.textContent = "dd/mm/aaaa";
    }

    document.getElementById("cal-salida").classList.remove("open");
  }

  document.getElementById("display-ingreso").addEventListener("click", (e) => {
    e.stopPropagation();
    openCal("ingreso");
  });

  document.getElementById("display-salida").addEventListener("click", (e) => {
    e.stopPropagation();
    openCal("salida");
  });

  document.addEventListener("click", () => {
    document
      .querySelectorAll(".cal-popup")
      .forEach((p) => p.classList.remove("open"));
  });
});
