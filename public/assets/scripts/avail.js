let weeklyAvailability = {};   // recurring rules (0–6)
let dateOverrides = {};        // YYYY-MM-DD overrides
function buildEvents() {
  const events = [];

  // Use FullCalendar's current view range
  const view = calendar.view;
  if (!view) return [];

  const start = new Date(view.activeStart);
  const end = new Date(view.activeEnd);

  // Loop through each day in the visible range
  for (let d = new Date(start); d < end; d.setDate(d.getDate() + 1)) {
    const dayOfWeek = d.getDay(); // 0-6
    const dateStr = toLocalDateString(d); // YYYY-MM-DD

    // Check overrides first, then weekly
    const data = dateOverrides[dateStr] ?? weeklyAvailability[dayOfWeek];

    const nextDay = new Date(d);
    nextDay.setDate(d.getDate() + 1);
    const nextDateStr = toLocalDateString(nextDay);

    if (!data || data.status === 0) {
      events.push({
        start: `${dateStr}T00:00:00`,
        end: `${nextDateStr}T00:00:00`,
        display: "background",
        backgroundColor: "rgba(238, 45, 45, 0.5)",
        extendedProps: {
          isDayOff: true
        }
      });
      continue;
    }

    events.push({
      title: "Available",
      start: `${dateStr}T${data.start}`,
      end: `${dateStr}T${data.end}`,
      backgroundColor: "#35bd57ff",
      extendedProps: {
        day: dayOfWeek,
        date: dateStr
      }
    });
  }
  return events;

}
async function fetchAvailability(start, end) {
  try {
    let url = `/admin/getAvailability`;
    if (start && end) {
      url += `?start=${encodeURIComponent(start)}&end=${encodeURIComponent(end)}`;
    }
    const res = await fetch(url);
    const rows = await res.json();

    weeklyAvailability = {};
    dateOverrides = {};

    rows.data.forEach(row => {
      if (row.is_recurring == 1) {
        // weekly rule
        weeklyAvailability[row.day_of_week] = {
          start: row.start_time?.slice(11, 19),
          end: row.end_time?.slice(11, 19),
          status: row.status
        };
      } else if (row.is_recurring == 0 && row.change_date) {
        // date override
        dateOverrides[row.change_date] = {
          start: row.start_time?.slice(11, 19),
          end: row.end_time?.slice(11, 19),
          status: row.status
        };
      }
    });

    refreshCalendar();

  } catch (err) {
    console.error("Fetch availability error:", err);
  }
}
// initailize full calender
const calendar = new FullCalendar.Calendar(
  document.getElementById("calendar"), {
  initialView: "timeGridWeek",
  firstDay: 1,  // Week starts on Monday
  allDaySlot: false,
  editable: false,
  selectable: false,
  events: [],

  datesSet: function (info) {
    fetchAvailability(info.startStr, info.endStr);
  },

  dateClick(info) {
    const clickedDate = new Date(info.dateStr.split("T")[0]);
    clickedDate.setHours(0, 0, 0, 0);
    const today = new Date();
    today.setHours(0, 0, 0, 0);

    // Prevent past dates
    if (clickedDate < today) {
      showAlert("Cannot modify past dates.", "error");
      return;
    }

    const day = info.date.getDay();
    const date = info.dateStr.split("T")[0];
    openModal(day, date);
  }
}
);

calendar.render();

function refreshCalendar() {
  calendar.removeAllEvents();
  calendar.addEventSource(buildEvents());
}
let selectedDay = null;
let selectedDate = null;
function openModal(day, date) {
  selectedDay = day;
  selectedDate = date;
  document.getElementById("modalDate").innerText = date;
  document.getElementById("modalDay").innerText =
    ["Sun", "Mon", "Tue", "Wed", "Thu", "Fri", "Sat"][day];

  const data = weeklyAvailability[day] || {};
  document.getElementById("dayStart").value = data.start || "";
  document.getElementById("dayEnd").value = data.end || "";

  document.getElementById("dayModal").style.display = "flex";
}

function closeModal() {
  document.getElementById("dayModal").style.display = "none";
}
document.getElementById("saveDay").onclick = async () => {
  weeklyAvailability[selectedDay] = {
    start: dayStart.value,
    end: dayEnd.value,
    status: 1
  };

  await saveSingleDayAvailability(selectedDay, selectedDate, weeklyAvailability[selectedDay]);

  // Fetch for current view range to verify/refresh
  const view = calendar.view;
  fetchAvailability(view.activeStart.toISOString(), view.activeEnd.toISOString());
};

document.getElementById("markOff").onclick = () => {
  setDayOff(selectedDay, selectedDate);
  closeModal();
};
async function setDayOff(dayOfWeek, date) {
  try {
    const response = await fetch("/admin/set-dayoff", {
      method: "POST",
      headers: {
        "Content-Type": "application/json"
      },
      body: JSON.stringify({
        day_of_week: dayOfWeek,
        change_date: date
      })
    });

    const data = await response.json();

    if (data.status === "error") {
      showAlert(data.message || "Failed to mark day off", "error");
      return;
    }

    // showAlert("Day marked as off", "success");

    // showAlert("Day marked as off", "success");

    // Refresh with current view
    const view = calendar.view;
    await fetchAvailability(view.activeStart.toISOString(), view.activeEnd.toISOString());

  } catch (err) {
    console.error("Set day off error:", err);
    showAlert("Could not set day off", "error");
  }
}

document.getElementById("applyAll").onclick = () => {
  const start = defaultStart.value;
  const end = defaultEnd.value;

  for (let d = 0; d <= 6; d++) {
    weeklyAvailability[d] = { start, end, status: 1 };
  }

  const weeklyData = { "start_time": weeklyAvailability[1]["start"], "end_time": weeklyAvailability[1]["end"], status: 1 };

  saveWeeklyAvailability(weeklyData);
  fetchAvailability();

};
async function saveWeeklyAvailability(weeklyAvailability) {
  try {
    const response = await fetch("/admin/add-weekAvailability", {
      method: "POST",
      headers: {
        "Content-Type": "application/json"
      },
      body: JSON.stringify({
        availability: weeklyAvailability
      })
    });

    const data = await response.json();

    if (data.status === "error") {
      showAlert(data.message || "Failed to save availability", "error");
      return;
    }
    showAlert(data.message || "availbility saved", "success");
    console.log("Weekly availability saved");

  } catch (error) {
    console.error("Save weekly availability error:", error);
    showAlert("Could not save availability. Please try again.");
  }
}
async function saveSingleDayAvailability(dayOfWeek, date, dayData) {
  try {
    const response = await fetch("/admin/update-dayAvailability", {
      method: "POST",
      headers: {
        "Content-Type": "application/json"
      },
      body: JSON.stringify({
        date: date,
        day_of_week: dayOfWeek,
        start_time: dayData.start || null,
        end_time: dayData.end || null,
        status: dayData.status
      })
    });

    const data = await response.json();

    if (data.status === "error") {
      showAlert(data.message || "Failed to save availability", "error");
      return;
    }
    console.log("Day availability saved");
    closeModal();
    return;

  } catch (error) {
    console.error("Save day availability error:", error);
    showAlert("Could not save day availability.");
  }
}
function toLocalDateString(date) {
  const y = date.getFullYear();
  const m = String(date.getMonth() + 1).padStart(2, "0");
  const d = String(date.getDate()).padStart(2, "0");
  return `${y}-${m}-${d}`;
}