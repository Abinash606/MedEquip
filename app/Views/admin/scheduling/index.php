<?= $this->extend('layouts/main') ?>


<?= $this->section('content') ?>
<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2  topbar">
            <div>
                <h3 class="fw-bold mb-0">Master Schedule</h3>
                <p class="text-muted small mb-0">Manage appointments, routes, and technician availability.</p>
            </div>
            <div class="d-flex gap-2 flex-wrap">
                <div class="btn-group b-group" role="group">
                    <button id="btnScheduleTimeline" class="btn btn-outline-secondary active">Timeline</button>
                    <button id="btnScheduleCalendar" class="btn btn-outline-secondary">Calendar</button>
                    <button id="btnScheduleMap" class="btn btn-outline-secondary">Map Route</button>
                </div>
                <div class="btn-group  b-group" role="group">
                    <button id="btnScheduleDay" class="btn btn-outline-secondary active">Day</button>
                    <button id="btnScheduleWeek" class="btn btn-outline-secondary">Week</button>
                    <button id="btnScheduleMonth" class="btn btn-outline-secondary">Month</button>
                </div>
            </div>
        </div>
 <div class="content">
        <div class="glass-card">
            <div class="d-flex justify-content-between align-items-center mb-4 border-bottom p-3 flex-wrap gap-2">
                <div class="d-flex gap-2 align-items-center">
                    <button class="btn btn-light btn-sm border"><i class="fa-solid fa-chevron-left"></i></button>
                    <span class="fw-bold mx-2">Today, Oct 24, 2025</span>
                    <button class="btn btn-light btn-sm border"><i class="fa-solid fa-chevron-right"></i></button>
                </div>
                <button class="btn btn-primary btn-new btn-sm" data-bs-toggle="modal" data-bs-target="#appointmentModal"><i class="fa-solid fa-plus"></i> Add Appointment</button>
            </div>

            <!-- Status legend and site information -->
            <div class="d-flex justify-content-between align-items-start mb-3 p-3">
                <div>
                    <span class="badge bg-success me-2">Ready</span>
                    <span class="badge bg-warning text-dark me-2">Needs Attention</span>
                    <span class="badge bg-danger">Past Due</span>
                </div>
                <div class="glass-card p-2" style="min-width: 200px;">
                    <h6 class="fw-bold mb-1">Mercy Hospital</h6>
                    <div class="small text-muted mb-1">123 Healthcare Blvd, New York, NY</div>
                    <div class="small"><i class="fa-solid fa-phone me-1"></i> 555-0123</div>
                </div>
            </div>

            <!-- Unified schedule views container -->
            <div id="scheduleViews">
                <!-- Timeline container holding day/week/month subviews -->
                <div id="timelineView" class="schedule-view active">
                    <!-- Day timeline using original Gantt-style bars -->
                    <div id="timelineDayView" class="timeline-view active">
                        <div class="gantt-container p-3">
                            <div class="d-flex ms-5 ps-5 mb-2 text-muted small">
                                <div style="width: 12.5%">8 AM</div>
                                <div style="width: 12.5%">10 AM</div>
                                <div style="width: 12.5%">12 PM</div>
                                <div style="width: 12.5%">2 PM</div>
                                <div style="width: 12.5%">4 PM</div>
                                <div style="width: 12.5%">6 PM</div>
                            </div>

                            <div class="gantt-row">
                                <div class="gantt-label">
                                    <img src="https://ui-avatars.com/api/?name=John+Doe" class="avatar-circle me-2"> John D.
                                </div>
                                <div class="gantt-timeline">
                                    <div class="gantt-bar bg-primary" style="left: 10%; width: 30%;" title="Mercy Hospital - MRI Repair">Mercy Hospital (MRI)</div>
                                    <div class="gantt-bar bg-success" style="left: 50%; width: 20%;" title="Clinic A - Routine">Clinic A (PM)</div>
                                </div>
                            </div>

                            <div class="gantt-row">
                                <div class="gantt-label">
                                    <img src="https://ui-avatars.com/api/?name=Sarah+Lee" class="avatar-circle me-2"> Sarah L.
                                </div>
                                <div class="gantt-timeline">
                                    <div class="gantt-bar bg-danger" style="left: 5%; width: 85%;" title="City Hospital - Emergency Repair">City Hospital - EMERGENCY</div>
                                </div>
                            </div>

                            <div class="gantt-row">
                                <div class="gantt-label">
                                    <img src="https://ui-avatars.com/api/?name=Mike+Ross" class="avatar-circle me-2"> Mike R.
                                </div>
                                <div class="gantt-timeline">
                                     <div class="gantt-bar bg-warning " style="left: 60%; width: 15%;" title="Travel">Travel</div>
                                     <div class="gantt-bar bg-primary" style="left: 75%; width: 20%;" title="Dental Plus - Install">Dental Plus</div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- Week timeline summarising events for each day -->
                    <div id="timelineWeekView" class="timeline-view">
                        <div class="list-group">
                            <div class="list-group-item">
                                <strong>Mon, Oct 20</strong>
                                <div class="small text-muted">John D. – Mercy Hospital (MRI Repair) – 9 AM – 12 PM</div>
                                <div class="small text-muted">Sarah L. – City Hospital (Emergency) – 8:30 AM – 6 PM</div>
                            </div>
                            <div class="list-group-item">
                                <strong>Tue, Oct 21</strong>
                                <div class="small text-muted">John D. – Clinic A (PM) – 1 PM – 3 PM</div>
                            </div>
                            <div class="list-group-item">
                                <strong>Thu, Oct 23</strong>
                                <div class="small text-muted">Mike R. – Dental Plus (Install) – 4 PM – 6:30 PM</div>
                            </div>
                        </div>
                    </div>
                    <!-- Month timeline summarising monthly appointment counts -->
                    <div id="timelineMonthView" class="timeline-view">
                        <div class="list-group">
                            <div class="list-group-item">
                                <strong>October 2025</strong>
                                <div class="small text-muted">4 scheduled appointments</div>
                            </div>
                            <div class="list-group-item">
                                <strong>November 2025</strong>
                                <div class="small text-muted">2 scheduled appointments</div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Calendar view using FullCalendar -->
                <div id="calendarView" class="schedule-view">
                    <div id="calendar"></div>
                </div>
                <!-- Map view using Leaflet -->
                <div id="mapView" class="schedule-view">
                    <div id="mapid" style="width: 100%; height: 450px;"></div>
                </div>
            </div>
        </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
document.addEventListener('DOMContentLoaded', function() {
    var calendarEl = document.getElementById('calendar');
    var calendar = new FullCalendar.Calendar(calendarEl, {
        initialView: 'dayGridMonth',
        headerToolbar: {
            left: 'prev,next today',
            center: 'title',
            right: 'dayGridMonth,timeGridWeek,listWeek'
        },
        events: '<?= base_url('scheduling/events') ?>',
        eventClick: function(info) {
            alert('Work Order: ' + info.event.title + '\nStatus: ' + info.event.extendedProps.status);
        }
    });
    calendar.render();
});
</script>
<?= $this->endSection() ?>
