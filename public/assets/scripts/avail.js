let weeklyAvailability = {};   // recurring rules (0–6)
let dateOverrides = {};        // YYYY-MM-DD overrides
function buildEvents() {
  const events = [];

  // Get Monday of current week
  const today = new Date();
  const day = today.getDay(); // 0 (Sun) - 6 (Sat)
  const diffToMonday = (day === 0 ? -6 : 1) - day;

  const monday = new Date(today);
  monday.setDate(today.getDate() + diffToMonday);

  for (let d = 0; d <= 6; d++) {
    const eventDate = new Date(monday);
    eventDate.setDate(monday.getDate() + d);

    // const dateStr = eventDate.toISOString().split("T")[0];
        const dateStr = toLocalDateString(eventDate);
const nextDay = new Date(eventDate);
nextDay.setDate(eventDate.getDate() + 1);
const nextDateStr = toLocalDateString(nextDay);
    const data =
      dateOverrides[dateStr] ??
      weeklyAvailability[d];

    if (!data || data.status === 0){
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
        day: d,
        date: dateStr
      }
    });
  }
  return events;

}
async function fetchAvailability() {
  try {
     const tz = "ASIA/Kolkata"; // Intl.DateTimeFormat().resolvedOptions().timeZone;
    const res = await fetch(`/admin/getAvailability?time_zone=${tz}`);
    const rows = await res.json();

    weeklyAvailability = {};
    dateOverrides = {};

    rows.data.forEach(row => {
      if (row.is_recurring == 1) {
        // weekly rule
        weeklyAvailability[row.day_of_week] = {
          start: row.start_time?.slice(0,5),
          end: row.end_time?.slice(0,5),
          status: row.status
        };
      } else if (row.is_recurring == 0 && row.change_date) {
        // date override (only current week already filtered by backend)
        dateOverrides[row.change_date] = {
          start: row.start_time?.slice(0,5),
          end: row.end_time?.slice(0,5),
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

    dateClick(info) {
      const day = info.date.getDay();
      const date = info.dateStr.split("T")[0];
      openModal(day, date);
    }
  }
);

calendar.render();
fetchAvailability();

let selectedDay = null;
let selectedDate = null;
function openModal(day,date) {
  selectedDay = day;
  selectedDate = date;
  document.getElementById("modalDate").innerText = date;
  document.getElementById("modalDay").innerText =
    ["Sun","Mon","Tue","Wed","Thu","Fri","Sat"][day];

  const data = weeklyAvailability[day] || {};
  document.getElementById("dayStart").value = data.start || "";
  document.getElementById("dayEnd").value = data.end || "";

  document.getElementById("dayModal").style.display = "flex";
}

function closeModal() {
  document.getElementById("dayModal").style.display = "none";
}
document.getElementById("saveDay").onclick = () => {
  weeklyAvailability[selectedDay] = {
    start: dayStart.value,
    end: dayEnd.value,
    status: 1
  };
  // refreshCalendar();
 saveSingleDayAvailability(selectedDay,selectedDate, weeklyAvailability[selectedDay]);
 fetchAvailability();
};

document.getElementById("markOff").onclick = () => {
  setDayOff(selectedDay,selectedDate);
  closeModal();
};
async function setDayOff(dayOfWeek,date) {
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

    await fetchAvailability();

  } catch (err) {
    console.error("Set day off error:", err);
    showAlert("Could not set day off", "error");
  }
}
function refreshCalendar() {
  calendar.removeAllEvents();
  calendar.addEventSource(buildEvents());
}
document.getElementById("applyAll").onclick = () => {
  const start = defaultStart.value;
  const end = defaultEnd.value;

  for (let d = 0; d <= 6; d++) {
    weeklyAvailability[d] = { start, end, status: 1 };
  }
  
  const weeklyData = {"start_time":weeklyAvailability[1]["start"], "end_time":weeklyAvailability[1]["end"], status:1};
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
      showAlert(data.message || "Failed to save availability","error");
      return;
    }
        showAlert(data.message || "availbility saved","success");
    console.log("Weekly availability saved");

  } catch (error) {
    console.error("Save weekly availability error:", error);
    showAlert("Could not save availability. Please try again.");
  }
}
async function saveSingleDayAvailability(dayOfWeek,date, dayData) {
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
      showAlert(data.message || "Failed to save availability","error");
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