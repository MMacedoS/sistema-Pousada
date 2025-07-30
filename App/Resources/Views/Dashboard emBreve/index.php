<?php 
    require_once __DIR__ . "/../layout/top.php";
?>

    <div class="container">
        <!-- Row start -->
        <div class="row gx-3">
            <div class="col-12 col-xl-6">
            <!-- Breadcrumb start -->
            <ol class="breadcrumb mb-3">
                <li class="breadcrumb-item">
                <i class="icon-house_siding lh-1"></i>
                <a href="index.html" class="text-decoration-none">Home</a>
                </li>
                <li class="breadcrumb-item">Dashboards</li>
                <li class="breadcrumb-item">Analytics</li>
            </ol>
            <!-- Breadcrumb end -->
            </div>
        </div>
        <!-- Row end -->

        <!-- Row start -->
        <div class="row gx-3">
            <div class="col-xl-6 col-sm-12 col-12">
            <div class="card mb-3">
                <div class="card-body">
                <!-- Row start -->
                <div class="row">
                    <div class="col-sm-8 col-12">
                    <h3 class="mb-3">Congratulations John 🎉</h3>
                    <p class="w-50">
                        You have resolved
                        <span class="text-success fw-bold">85%</span> more
                        tickets than last year.
                    </p>
                    <div id="tickets"></div>
                    </div>
                    <div class="col-sm-4 col-12">
                    <div class="text-end">
                        <img src="<?= __DIR__ . "/../Public/admin"?>/assets/images/sales.svg" class="img-150" alt="Bootstrap Gallery"/>
                    </div>
                    <div class="mt-5 d-flex flex-wrap gap-3">
                        <div class="d-flex align-items-center">
                        <div class="icons-box md bg-info rounded-3 me-3">
                            <i class="icon-add_task text-white fs-4"></i>
                        </div>
                        <div class="m-0">
                            <h3 class="m-0 fw-semibold">960</h3>
                            <p class="m-0 text-secondary">Resolved in 2024</p>
                        </div>
                        </div>
                        <div class="d-flex align-items-center">
                        <div class="icons-box md bg-danger rounded-3 me-3">
                            <i class="icon-add_task text-white fs-4"></i>
                        </div>
                        <div class="m-0">
                            <h3 class="m-0 fw-semibold">630</h3>
                            <p class="m-0 text-secondary">Resolved in 2023</p>
                        </div>
                        </div>
                    </div>
                    </div>
                </div>
                <!-- Row end -->
                </div>
            </div>
            </div>
            <div class="col-xl-3 col-sm-6 col-12">
            <div class="card mb-3">
                <div class="card-body">
                <h5 class="card-title">Calls</h5>
                <div id="calls"></div>
                </div>
            </div>
            <div class="card mb-3">
                <div class="card-body">
                <div class="d-flex flex-row">
                    <div class="d-flex align-items-center">
                    <div class="border border-info rounded-4 icons-box md">
                        <i class="icon-support_agent text-info fs-3"></i>
                    </div>
                    <div class="ms-2">
                        <h3 class="m-0">49</h3>
                        <p class="m-0 text-secondary">Agents Online</p>
                    </div>
                    </div>
                    <div class="ms-auto">
                    <div id="sparkline1"></div>
                    </div>
                </div>
                </div>
            </div>
            </div>
            <div class="col-xl-3 col-sm-6 col-12">
            <div class="card mb-3">
                <div class="card-body">
                <h5 class="card-title">Tickets Resolved</h5>
                <div id="callsByCountry" class="auto-align-graph"></div>
                </div>
            </div>
            </div>
        </div>
        <!-- Row end -->

        <!-- Row start -->
        <div class="row gx-3">
            <div class="col-lg-12 col-12">
            <div class="card mb-3">
                <div class="card-header">
                <h5 class="card-title">Tickets</h5>
                </div>
                <div class="card-body">
                <div class="table-outer">
                    <div class="table-responsive">
                    <table class="table truncate align-middle">
                        <thead>
                        <tr>
                            <th>#</th>
                            <th>Type</th>
                            <th>Status</th>
                            <th>Tags</th>
                            <th>Created Date</th>
                            <th>Last Reply</th>
                            <th>Priority</th>
                            <th>Department</th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr>
                            <td>1</td>
                            <td>Alignment UI issue fix</td>
                            <td><span class="badge bg-danger">In Progress</span></td>
                            <td>
                            <span class="badge border border-danger text-danger">Bug</span>
                            <span class="badge border border-danger text-danger">Design</span>
                            </td>
                            <td>2023/04/25</td>
                            <td>2 mins ago</td>
                            <td><span class="badge bg-danger">High</span></td>
                            <td>Sales</td>
                        </tr>
                        <tr>
                            <td>2</td>
                            <td>Responsive Design Issues Fix</td>
                            <td><span class="badge bg-dark">Not Started</span></td>
                            <td>
                            <span class="badge border border-danger text-danger">Sales</span>
                            <span class="badge border border-danger text-danger">Testing</span>
                            </td>
                            <td>2023/02/12</td>
                            <td>7 mins ago</td>
                            <td><span class="badge bg-danger">Medium</span></td>
                            <td>Support</td>
                        </tr>
                        <tr>
                            <td>3</td>
                            <td>Unit Testing</td>
                            <td><span class="badge bg-danger">Feedback</span></td>
                            <td>
                            <span class="badge border border-danger text-danger">Fix</span>
                            <span class="badge border border-danger text-danger">Sales</span>
                            </td>
                            <td>2023/03/16</td>
                            <td>12 mins ago</td>
                            <td><span class="badge bg-danger">Low</span></td>
                            <td>Development</td>
                        </tr>
                        <tr>
                            <td>4</td>
                            <td>Validations</td>
                            <td><span class="badge bg-danger">In Progress</span></td>
                            <td>
                            <span class="badge border border-danger text-danger">Bug</span>
                            <span class="badge border border-dark text-dark">Development</span>
                            </td>
                            <td>2023/04/25</td>
                            <td>45 mins ago</td>
                            <td><span class="badge bg-danger">High</span></td>
                            <td>Sales</td>
                        </tr>
                        <tr>
                            <td>5</td>
                            <td>Testing and UI Issues Fix</td>
                            <td><span class="badge bg-danger">Testing</span></td>
                            <td>
                            <span class="badge border border-danger text-danger">Validation</span>
                            <span class="badge border border-danger text-danger">Fix</span>
                            </td>
                            <td>2023/02/12</td>
                            <td>58 mins ago</td>
                            <td><span class="badge bg-dark">Low</span></td>
                            <td>Support</td>
                        </tr>
                        </tbody>
                    </table>
                    </div>
                </div>
                </div>
            </div>
            </div>
        </div>
        <!-- Row end -->

        <!-- Row start -->
        <div class="row gx-3">
            <div class="col-12">
            <div class="card mb-3">
                <div class="card-body">
                <!-- Row start -->
                <div class="row g-4">
                    <div class="px-0 border-end col-xl-3 col-sm-6">
                    <div class="text-center">
                        <p class="m-0 small">Gross Profit</p>
                        <h3 class="my-2">75%</h3>
                        <p class="m-0 small">
                        <span class="badge bg-danger me-1">
                            <i class="bi bi-arrow-down-left-square"></i>
                            1.99%</span>
                        for Last month
                        </p>
                    </div>
                    </div>
                    <div class="px-0 border-end col-xl-3 col-sm-6">
                    <div class="text-center">
                        <p class="m-0 small">Opex Ratio</p>
                        <h3 class="my-2">62%</h3>
                        <p class="m-0 small">
                        <span class="badge bg-danger me-1">
                            <i class="bi bi-arrow-up-right-square"></i>
                            1.69%</span>
                        for Last month
                        </p>
                    </div>
                    </div>
                    <div class="px-0 border-end col-xl-3 col-sm-6">
                    <div class="text-center">
                        <p class="m-0 small">
                        Operating Profit
                        </p>
                        <h3 class="my-2">48%</h3>
                        <p class="m-0 small">
                        <span class="badge bg-danger me-1">
                            <i class="bi bi-arrow-up-right-square"></i>
                            2.9%</span>
                        for Last month
                        </p>
                    </div>
                    </div>
                    <div class="px-0 col-xl-3 col-sm-6">
                    <div class="text-center">
                        <p class="m-0 small">Net Profit</p>
                        <h3 class="my-2">32%</h3>
                        <p class="m-0 small">
                        <span class="badge bg-dark me-1">
                            <i class="bi bi-arrow-up-right-square"></i>
                            18.5%</span>
                        for Last month
                        </p>
                    </div>
                    </div>
                </div>
                <!-- Row end -->
                </div>
            </div>
            </div>
        </div>
        <!-- Row end -->

        <!-- Row start -->
        <div class="row gx-3">
            <div class="col-lg-12 col-12">
            <div class="card mb-3">
                <div class="card-header">
                <h5 class="card-title">Tasks</h5>
                </div>
                <div class="card-body">
                <div id="tasks"></div>
                </div>
            </div>
            </div>
        </div>
        <!-- Row end -->

        <!-- Row start -->
        <div class="row gx-3">
            <div class="col-lg-6 col-12">
            <div class="card mb-3">
                <div class="card-header">
                <h5 class="card-title">Priority</h5>
                </div>
                <div class="card-body">
                <div id="ticketsData"></div>
                </div>
            </div>
            </div>
            <div class="col-lg-6 col-12">
            <div class="card mb-3">
                <div class="card-header">
                <h5 class="card-title">Avg. Response Time</h5>
                </div>
                <div class="card-body">
                <div id="avgTimeData"></div>
                </div>
            </div>
            </div>
        </div>
        <!-- Row end -->

        <!-- Row start -->
        <div class="row gx-3">
            <div class="col-sm-3 col-12">
            <div class="card mb-3">
                <div class="card-body">
                <div class="d-flex mb-2">
                    <div class="icons-box md bg-primary rounded-5 me-3">
                    <i class="icon-add_task fs-4 text-white"></i>
                    </div>
                    <div class="d-flex flex-column">
                    <h2 class="m-0 lh-1">18</h2>
                    <p class="m-0 opacity-50">Tickets</p>
                    </div>
                </div>
                <div class="m-0">
                    <div class="progress thin mb-2">
                    <div class="progress-bar bg-primary" role="progressbar" style="width: 60%" aria-valuenow="60"
                        aria-valuemin="0" aria-valuemax="100"></div>
                    </div>
                    <p class="m-0 small fw-light opacity-75">60 percent completed.</p>
                </div>
                </div>
            </div>
            </div>
            <div class="col-sm-3 col-12">
            <div class="card mb-3">
                <div class="card-body">
                <div class="d-flex mb-2">
                    <div class="icons-box md bg-info rounded-5 me-3">
                    <i class="icon-add_task fs-4 text-white"></i>
                    </div>
                    <div class="d-flex flex-column">
                    <h2 class="m-0 lh-1">09</h2>
                    <p class="m-0 opacity-50">InProgress</p>
                    </div>
                </div>
                <div class="m-0">
                    <div class="progress thin mb-2">
                    <div class="progress-bar bg-info" role="progressbar" style="width: 70%" aria-valuenow="70"
                        aria-valuemin="0" aria-valuemax="100"></div>
                    </div>
                    <p class="m-0 small fw-light opacity-75">70 percent completed.</p>
                </div>
                </div>
            </div>
            </div>
            <div class="col-sm-3 col-12">
            <div class="card mb-3">
                <div class="card-body">
                <div class="d-flex mb-2">
                    <div class="icons-box md bg-danger rounded-5 me-3">
                    <i class="icon-add_task fs-4 text-white"></i>
                    </div>
                    <div class="d-flex flex-column">
                    <h2 class="m-0 lh-1">07</h2>
                    <p class="m-0 opacity-50">On Hold</p>
                    </div>
                </div>
                <div class="m-0">
                    <div class="progress thin mb-2">
                    <div class="progress-bar bg-danger" role="progressbar" style="width: 80%" aria-valuenow="80"
                        aria-valuemin="0" aria-valuemax="100"></div>
                    </div>
                    <p class="m-0 small fw-light opacity-75">80 percent completed.</p>
                </div>
                </div>
            </div>
            </div>
            <div class="col-sm-3 col-12">
            <div class="card mb-3">
                <div class="card-body">
                <div class="d-flex mb-2">
                    <div class="icons-box md bg-success rounded-5 me-3">
                    <i class="icon-add_task fs-4 text-white"></i>
                    </div>
                    <div class="d-flex flex-column">
                    <h2 class="m-0 lh-1">45</h2>
                    <p class="m-0 opacity-50">Completed</p>
                    </div>
                </div>
                <div class="m-0">
                    <div class="progress thin mb-2">
                    <div class="progress-bar bg-success" role="progressbar" style="width: 90%" aria-valuenow="90"
                        aria-valuemin="0" aria-valuemax="100"></div>
                    </div>
                    <p class="m-0 small fw-light opacity-75">90 percent completed.</p>
                </div>
                </div>
            </div>
            </div>
        </div>

        <div class="row gx-3">
            <div class="col-lg-3 col-sm-6 col-12">
            <div class="card mb-3">
                <div class="card-header">
                <h5 class="card-title">Today's Tickets</h5>
                </div>
                <div class="card-body">
                <i class="icon-stacked_line_chart display-3 opacity-25 position-absolute end-0 top-0 me-3"></i>
                <div class="d-flex justify-content-between mb-2">
                    <span>Completed</span>
                    <span class="fw-bold">50%</span>
                </div>
                <div class="progress small">
                    <div class="progress-bar bg-info" role="progressbar" style="width: 50%" aria-valuenow="50"
                    aria-valuemin="0" aria-valuemax="100"></div>
                </div>
                </div>
            </div>
            </div>
            <div class="col-lg-3 col-sm-6 col-12">
            <div class="card mb-3">
                <div class="card-header">
                <h5 class="card-title">New</h5>
                </div>
                <div class="card-body">
                <i class="icon-stacked_line_chart display-3 opacity-25 position-absolute end-0 top-0 me-3"></i>
                <div class="d-flex justify-content-between mb-2">
                    <span>Assigned</span>
                    <span class="fw-bold">70%</span>
                </div>
                <div class="progress small">
                    <div class="progress-bar bg-info" role="progressbar" style="width: 70%" aria-valuenow="70"
                    aria-valuemin="0" aria-valuemax="100"></div>
                </div>
                </div>
            </div>
            </div>
            <div class="col-lg-3 col-sm-6 col-12">
            <div class="card mb-3">
                <div class="card-header">
                <h5 class="card-title">Closed</h5>
                </div>
                <div class="card-body">
                <i class="icon-stacked_line_chart display-3 opacity-25 position-absolute end-0 top-0 me-3"></i>
                <div class="d-flex justify-content-between mb-2">
                    <span>Overall</span>
                    <span class="fw-bold">90%</span>
                </div>
                <div class="progress small">
                    <div class="progress-bar bg-info" role="progressbar" style="width: 90%" aria-valuenow="90"
                    aria-valuemin="0" aria-valuemax="100"></div>
                </div>
                </div>
            </div>
            </div>
            <div class="col-lg-3 col-sm-6 col-12">
            <div class="card mb-3">
                <div class="card-header">
                <h5 class="card-title">Completed</h5>
                </div>
                <div class="card-body">
                <i class="icon-stacked_line_chart display-3 opacity-25 position-absolute end-0 top-0 me-3"></i>
                <div class="d-flex justify-content-between mb-2">
                    <span>Done</span>
                    <span class="fw-bold">100%</span>
                </div>
                <div class="progress small">
                    <div class="progress-bar bg-danger" role="progressbar" style="width: 100%" aria-valuenow="100"
                    aria-valuemin="0" aria-valuemax="100"></div>
                </div>
                </div>
            </div>
            </div>
        </div>
        <!-- Row end -->
    </div>


<?php 
    require_once __DIR__ . "/../layout/bottom.php";
?>


<!-- Overlay Scroll JS -->
<script src="<?= $_ENV['URL_PREFIX_APP'] . "/Public/admin"?>/assets/vendor/overlay-scroll/jquery.overlayScrollbars.min.js"></script>
<script src="<?= $_ENV['URL_PREFIX_APP'] . "/Public/admin"?>/assets/vendor/overlay-scroll/custom-scrollbar.js"></script>

<!-- Apex Charts -->
<script src="<?= $_ENV['URL_PREFIX_APP'] . "/Public/admin"?>/assets/vendor/apex/apexcharts.min.js"></script>
<script src="<?= $_ENV['URL_PREFIX_APP'] . "/Public/admin"?>/assets/vendor/apex/custom/home/tasks.js"></script>
<script src="<?= $_ENV['URL_PREFIX_APP'] . "/Public/admin"?>/assets/vendor/apex/custom/home/ticketsData.js"></script>
<script src="<?= $_ENV['URL_PREFIX_APP'] . "/Public/admin"?>/assets/vendor/apex/custom/home/avgTimeData.js"></script>
<script src="<?= $_ENV['URL_PREFIX_APP'] . "/Public/admin"?>/assets/vendor/apex/custom/home/tickets.js"></script>
<script src="<?= $_ENV['URL_PREFIX_APP'] . "/Public/admin"?>/assets/vendor/apex/custom/home/calls.js"></script>
<script src="<?= $_ENV['URL_PREFIX_APP'] . "/Public/admin"?>/assets/vendor/apex/custom/home/callsByCountry.js"></script>
<script src="<?= $_ENV['URL_PREFIX_APP'] . "/Public/admin"?>/assets/vendor/apex/custom/home/sparkline.js"></script>