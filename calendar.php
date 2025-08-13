<?php 
include 'header.php'; 
include 'db.php';

// Fetch events from the database for FullCalendar
$events_result = $conn->query("SELECT title, start_datetime as start, description FROM events ORDER BY start_datetime ASC");
$events = [];
while ($row = $events_result->fetch_assoc()) {
    // We need to add an 'extendedProps' key for custom data like the description
    $events[] = [
        'title' => $row['title'],
        'start' => $row['start'],
        'extendedProps' => [
            'description' => $row['description']
        ]
    ];
}
?>
<script src='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.11/index.global.min.js'></script>

<div>
    <h1 class="text-3xl font-bold text-gray-800 dark:text-white mb-6">Event Calendar</h1>
    <div class="bg-white dark:bg-gray-800/50 rounded-xl shadow-md p-4">
        <div id='calendar'></div>
    </div>
</div>

<!-- Event Detail Modal -->
<div id="event-modal" class="fixed inset-0 bg-black bg-opacity-60 z-50 flex justify-center items-center p-4 hidden">
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-2xl w-full max-w-lg" onclick="event.stopPropagation()">
        <div class="flex justify-between items-center p-4 border-b border-gray-200 dark:border-gray-700">
            <h2 id="modal-title" class="text-xl font-bold text-gray-800 dark:text-white">Event Details</h2>
            <button id="close-modal-btn" class="p-2 rounded-full hover:bg-gray-100 dark:hover:bg-gray-700">
                <i class="fa-solid fa-times"></i>
            </button>
        </div>
        <div class="p-6">
            <p class="text-lg font-semibold text-gray-700 dark:text-gray-300">Date & Time:</p>
            <p id="modal-date" class="mb-4 text-gray-600 dark:text-gray-400"></p>
            <p class="text-lg font-semibold text-gray-700 dark:text-gray-300">Description:</p>
            <p id="modal-description" class="text-gray-600 dark:text-gray-400"></p>
        </div>
    </div>
</div>


<script>
  document.addEventListener('DOMContentLoaded', function() {
    var calendarEl = document.getElementById('calendar');
    var modal = document.getElementById('event-modal');
    var closeModalBtn = document.getElementById('close-modal-btn');
    var modalTitle = document.getElementById('modal-title');
    var modalDate = document.getElementById('modal-date');
    var modalDescription = document.getElementById('modal-description');

    var calendar = new FullCalendar.Calendar(calendarEl, {
      initialView: 'dayGridMonth',
      headerToolbar: {
        left: 'prev,next today',
        center: 'title',
        right: 'dayGridMonth,timeGridWeek,listWeek'
      },
      events: <?php echo json_encode($events); ?>,
      eventClick: function(info) {
        info.jsEvent.preventDefault(); // don't let the browser navigate
        
        modalTitle.textContent = info.event.title;
        modalDate.textContent = info.event.start.toLocaleString(undefined, {
            dateStyle: 'full',
            timeStyle: 'short'
        });
        modalDescription.textContent = info.event.extendedProps.description || 'No description available.';
        
        modal.classList.remove('hidden');
      }
    });

    function closeModal() {
        modal.classList.add('hidden');
    }

    closeModalBtn.addEventListener('click', closeModal);
    modal.addEventListener('click', closeModal);

    calendar.render();
  });
</script>

<?php include 'footer.php'; ?>
