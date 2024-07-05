document.addEventListener("DOMContentLoaded", function () {
  // Initialize FullCalendar separately
  var calendarEl = document.getElementById("calendar");
  var calendar = new FullCalendar.Calendar(calendarEl, {
    initialView: "dayGridMonth",
    selectable: false, // Disable selecting dates

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
      // Retrieve session details when an event is clicked
      var session = info.event;
      var paymentStatusText = session.extendedProps.paymentStatus;

      // Format session date
      var sessionDate = new Date(session.start);
      var options = { month: "long", day: "numeric", year: "numeric" };
      var formattedDate = sessionDate.toLocaleDateString("en-US", options);

      // Populate session details
      document.getElementById("sessionID").textContent =
        session.extendedProps.sessionID;
      document.getElementById("sessionDate").textContent = formattedDate;
      document.getElementById("sessionBody").textContent = session.title;
      document.getElementById("paymentStatus").textContent = paymentStatusText;

      // Show or hide the paid button based on payment status
      var paidButtonContainer = document.getElementById("paidButtonContainer");
      if (paymentStatusText === "unpaid") {
        paidButtonContainer.style.display = "block";
        document.getElementById("paymentToggleButton").textContent = "PAID";
      } else {
        paidButtonContainer.style.display = "none";
      }

      // Show the modal with session details
      var modal = document.getElementById("sessionDetails");
      modal.style.display = "block";

      // Close the modal when the user clicks on <span> (x)
      var closeButton = document.querySelector(
        ".sessionDetails-content .close"
      );
      closeButton.onclick = function () {
        var modal = document.getElementById("sessionDetails");
        modal.style.display = "none";
      };

      // Close the modal when the user clicks anywhere outside of it
      window.onclick = function (event) {
        var modal = document.getElementById("sessionDetails");
        if (event.target == modal) {
          modal.style.display = "none";
        }
      };
    },
  });

  calendar.render();

  // Close the modal when the user clicks on <span> (x)
  var closeButton = document.getElementsByClassName("close")[0];
  closeButton.onclick = function () {
    var modal = document.getElementById("sessionDetailsModal");
    modal.style.display = "none";
  };

  // Close the modal when the user clicks anywhere outside of it
  window.onclick = function (event) {
    var modal = document.getElementById("sessionDetailsModal");
    if (event.target == modal) {
      modal.style.display = "none";
    }
  };
});

function savePaymentStatus() {
  var sessionID = document.getElementById("sessionID").innerText;
  var newPaymentStatus = document
    .getElementById("paymentStatus")
    .textContent.toUpperCase();

  // Make AJAX request to update payment status
  var xhr = new XMLHttpRequest();
  xhr.open("POST", "update_payment_status.php", true);
  xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
  xhr.onreadystatechange = function () {
    if (xhr.readyState === XMLHttpRequest.DONE) {
      if (xhr.status === 200) {
        // Update payment status in the modal if successful
        var paymentStatusElement = document.getElementById("paymentStatus");
        paymentStatusElement.textContent = newPaymentStatus;
        paymentStatusElement.dataset.status = newPaymentStatus;
        alert("Payment status updated successfully!");

        // Reload the page after successful update
        location.reload();
      } else {
        alert("Failed to update payment status!");
      }
    }
  };
  // Send sessionID and newPaymentStatus as POST data
  xhr.send("sessionID=" + sessionID + "&newPaymentStatus=" + newPaymentStatus);
}
