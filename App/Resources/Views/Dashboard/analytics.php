<?php require_once __DIR__ . '/../layout/top.php'; ?>

<!-- Row start -->
  <div class="row gx-3">
    <div class="col-sm-3 col-12">
      <a href="{{route('admin.news')}}">
        <div class="card mb-3">
          <div class="card-body">
            <div class="d-flex mb-2">
              <div class="icons-box md bg-primary rounded-5 me-3">
                <i class="icon-add_task fs-4 text-white"></i>
              </div>
              <div class="d-flex flex-column">                
                <h2 class="m-0 lh-1 d-block d-sm-none">Apartamentos Disponiveis</h2>
                <h2 class="m-0 lh-1 d-none d-xl-block d-lg-block d-md-block">Apto. Disponiveis</h2>
                <p class="m-0 opacity-50"></p>
               </div>
            </div>
            <div class="m-0">
                <div class="progress thin mb-2">
                <div class="progress-bar bg-primary" role="progressbar" style="width: 60%" aria-valuenow="60"
                    aria-valuemin="0" aria-valuemax="100"></div>
                </div>
                <p class="m-0 small fw-light opacity-75">60%.</p>
            </div>
          </div>
        </div>
      </a>
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
          <div class="m-0 d-none">
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
          <div class="m-0 d-none">
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
          <div class="m-0 d-none">
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

<?php require_once __DIR__ . '/../layout/bottom.php'; ?>
