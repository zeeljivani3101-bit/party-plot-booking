<?php
session_start();
require_once '../config/db.php';

// Fetch all events for the calendar
$sql = "
    SELECT b.id, c.customer_name, b.event_type, b.booking_date, b.status 
    FROM bookings b
    LEFT JOIN customers c ON b.customer_id = c.id
    WHERE b.status != 'Cancelled'
";
$result = $conn->query($sql);

$events = [];
if ($result && $result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $color = '#10B981'; // Green for confirmed/completed
        if($row['status'] == 'Pending') $color = '#F59E0B'; // Orange for pending
        
        $events[] = [
            'title' => $row['customer_name'] . ' - ' . $row['event_type'],
            'start' => $row['booking_date'],
            'color' => $color,
            'url' => '../admin/view_bookings.php' // Link to booking details
        ];
    }
}
$events_json = json_encode($events);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Event Calendar | PartyPlot Admin</title>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&family=Playfair+Display:ital,wght@0,400;0,600;0,700;1,400&display=swap" rel="stylesheet">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <link rel="stylesheet" href="../assets/css/style.css?v=<?php echo time(); ?>">
    
    <!-- FullCalendar CDN -->
    <script src='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.10/index.global.min.js'></script>
    <style>
        #calendar {
            max-width: 100%;
            margin: 0 auto;
            background: rgba(255, 255, 255, 0.05);
            padding: 1rem;
            border-radius: 15px;
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.1);
        }
        .fc-theme-standard th {
            padding: 10px 0;
            background: rgba(0,0,0,0.2);
            border-color: rgba(255,255,255,0.1);
        }
        .fc-theme-standard td {
            border-color: rgba(255,255,255,0.1);
        }
        .fc .fc-toolbar-title {
            color: var(--text-light);
            font-family: 'Playfair Display', serif;
        }
        .fc .fc-button-primary {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
        }
        .fc .fc-daygrid-day-number {
            color: var(--text-light);
            padding: 8px;
        }
        .fc .fc-event {
            cursor: pointer;
            border: none;
            padding: 2px 5px;
            border-radius: 4px;
        }
    </style>
    <script>
      document.addEventListener('DOMContentLoaded', function() {
        var calendarEl = document.getElementById('calendar');
        var eventsData = <?php echo $events_json; ?>;
        
        var calendar = new FullCalendar.Calendar(calendarEl, {
          initialView: 'dayGridMonth',
          headerToolbar: {
            left: 'prev,next today',
            center: 'title',
            right: 'dayGridMonth,timeGridWeek,listWeek'
          },
          events: eventsData,
          eventClick: function(info) {
              // Can handle custom clicks here if needed
          }
        });
        calendar.render();
      });
    </script>
</head>
<body>

<div class="admin-layout">
    <?php include '../admin/includes/sidebar.php'; ?>

    <main class="main-content">
        <div class="page-header">
            <div>
                <h1 class="page-title">Event Calendar</h1>
                <p style="color: var(--text-muted);">Visual overview of all scheduled events.</p>
            </div>
            <div style="display: flex; gap: 1rem;">
                <a href="send_bulk_reminders.php?type=all" class="btn btn-primary" style="background: #10B981; color: white; box-shadow: none;">
                    <i class='bx bx-bell'></i> Send General Update to All
                </a>
            </div>
        </div>

        <div class="glass" style="padding: 1.5rem;">
            <div id='calendar'></div>
        </div>
    </main>
</div>

</body>
</html>
