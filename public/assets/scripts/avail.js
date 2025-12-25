const weeklyAvailability = {
  1: { start: "10:00", end: "18:00", status: 1 }, // Mon
  2: { start: "10:00", end: "18:00", status: 1 },
  3: { start: "10:00", end: "18:00", status: 1 },
  4: { start: "10:00", end: "18:00", status: 1 },
  5: { start: "10:00", end: "18:00", status: 1 },
  6: { status: 0 }, // Sat off
  0: { status: 0 }  // Sun off
};
function buildEvents() {
  const events = [];

  // Get Monday of current week
  const today = new Date();
  const day = today.getDay(); // 0 (Sun) - 6 (Sat)
  const diffToMonday = (day === 0 ? -6 : 1) - day;

  const monday = new Date(today);
  monday.setDate(today.getDate() + diffToMonday);

  Object.entries(weeklyAvailability).forEach(([weekday, data]) => {
    if (data.status === 0) return;

    const eventDate = new Date(monday);
    eventDate.setDate(monday.getDate() + Number(weekday) - 1);

    const dateStr = eventDate.toISOString().split("T")[0];

    events.push({
      title: "Available",
      start: `${dateStr}T${data.start}`,
      end: `${dateStr}T${data.end}`,
      backgroundColor: "#35bd57ff",
      extendedProps: { day: weekday }
    });
  });

  return events;
}
const calendar = new FullCalendar.Calendar(document.getElementById("calendar"), {
  initialView: "timeGridWeek",
  allDaySlot: false,
  selectable: false,
  editable: false,
  events: buildEvents(),

  dateClick(info) {
    const day = info.date.getDay();
    openModal(day);
  }
});

calendar.render();
let selectedDay = null;

function openModal(day) {
  selectedDay = day;
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
  refreshCalendar();
 saveSingleDay(selectedDay, weeklyAvailability[selectedDay]);
  closeModal();
};

document.getElementById("markOff").onclick = () => {
  weeklyAvailability[selectedDay] = { status: 0 };
  refreshCalendar();
  closeModal();
};

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
  refreshCalendar();
  const weeklyData = {"start_time":weeklyAvailability[1]["start"], "end_time":weeklyAvailability[1]["end"], status:1};
    saveWeeklyAvailability(weeklyData);

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
async function saveSingleDayAvailability(dayOfWeek, dayData) {
  try {
    const response = await fetch("/admin/update-singleday", {
      method: "POST",
      headers: {
        "Content-Type": "application/json"
      },
      body: JSON.stringify({
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

    console.log(`Day ${dayOfWeek} saved`);
    return true;

  } catch (error) {
    console.error("Save day availability error:", error);
    showAlert("Could not save day availability.");
    return false;
  }
}
