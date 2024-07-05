document.addEventListener("DOMContentLoaded", function () {
  // Initialize FullCalendar separately
  var calendarEl = document.getElementById("calendar");
  var calendar = new FullCalendar.Calendar(calendarEl, {
    initialView: "dayGridMonth",
    selectable: true,

    select: function (info) {
      var today = new Date().toISOString().slice(0, 10);
      if (info.startStr === today) {
        showSessionForm(info.startStr);
      } else {
        alert("You can only add sessions for today.");
      }
    },

    events: {
      url: "fetch_sessions.php",
      method: "GET",
      extraParams: {
        memberID: memberID,
      },
      success: function (data) {
        console.log("Events:", data);
      },
      failure: function () {
        alert("There was an error while fetching sessions!");
      },
    },

    headerToolbar: {
      left: "prev,next today",
      center: "title",
      right: "dayGridMonth,listMonth",
    },

    eventClick: function (info) {
      var today = new Date().toISOString().slice(0, 10);
      if (info.event.startStr === today) {
        if (confirm("Are you sure you want to delete this session?")) {
          var eventId = info.event.id;
          deleteSession(eventId);
        }
      } else {
        alert("You can only delete sessions scheduled for today.");
      }
    },
  });

  calendar.render();

  function showSessionForm(date) {
    document.getElementById("session-form").style.display = "block";
    document.getElementById("session-date").textContent = date;
    document.getElementById("session-date-input").value = date;
  }

  if (sessionId !== null) {
    deleteSession(sessionId);
  }

  if (sessionId !== null) {
    deleteSession(sessionId);
  }

  function deleteSession(sessionId) {
    var xhr = new XMLHttpRequest();
    xhr.open("POST", "delete_session.php", true);
    xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
    xhr.onreadystatechange = function () {
      if (xhr.readyState === XMLHttpRequest.DONE) {
        if (xhr.status === 200) {
          var response = JSON.parse(xhr.responseText);
          if (response.canDelete) {
            alert("Session deleted successfully.");
            window.location.href = "memberMainPage.php";
          } else {
            alert(response.error || "Session cannot be deleted.");
          }
        } else {
          alert("Error deleting session. Please try again.");
        }
      }
    };

    xhr.send("sessionId=" + sessionId);
  }
});

function cancelSession() {
  document.getElementById("session-form").style.display = "none";
}
