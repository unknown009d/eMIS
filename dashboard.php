<?php
  $cpage="Dashboard";
  include 'template/header.php';
  $totalIT = count(selectQ($conn, "SELECT * FROM tbl_register")) ?? 0;
  $cancelledIT = count(selectQ($conn, "SELECT * FROM tbl_register WHERE j_status='cancelled' ")) ?? 0;
  $totalPP = count(selectQ($conn, "SELECT * FROM project_all")) ?? 0;
  $projectCompleted = count(selectQ($conn, "SELECT * FROM project_all WHERE certificate IS NOT NULL")) ?? 0;
  $totalGP = count(selectQ($conn, "SELECT * FROM tbl_gatepass")) ?? 0;
?>

<div class="col p-4 bg-light" style="z-index: 2;">

  <div class="container mt-2 dashboard">

  <h2 class="fw-bold mb-4">Welcome back,</h2>
  <div class="row">
    <div class="col-lg-4 col-md-6 col-sm-12 mb-4">
      <div class="card text-bg-primary h-100">
        <i class="bi bi-ui-checks-grid iconbackdrop"></i>
        <div class="card-body pb-2">
          <h5 class="card-title fw-bold">Service Requests</h5>
          <p class="card-text small">
            <?php
            // foreach ($jobtype as $type) {
            //   $noOfRequests = selectQ($conn, "SELECT COUNT(*) AS cnt FROM tbl_register WHERE j_type = ?", [$type['jtcode']]);
            //   echo $type["jtcode"] . "-[" . $noOfRequests[0]['cnt'] . "]&nbsp;&nbsp;";
            // }
            ?>
            <?= "[" . $totalIT . "] Total Requests";?>
            <?= $cancelledIT != 0 ? '<br>['.$cancelledIT . "] cancelled" : '' ?>
          </p>
        </div>
        <div class="card-footer border-0 pt-0 pb-3 background-color-0">
          <div class="d-flex justify-content-end align-items-start gap-2">
            <a href="jobType" class="btn btn-darker-cus"><i class="bi bi-journal-bookmark text-white"></i></a>
            <a href="servicesView" class="btn btn-darker-cus"><i class="bi bi-chevron-right text-white"></i></a>
          </div>
        </div>
      </div>
    </div>

    <div class="col-lg-4 col-md-6 col-sm-12 mb-4">
      <div class="card text-bg-success h-100">
        <i class="bi bi-server iconbackdrop"></i>
        <div class="card-body">
          <h5 class="card-title fw-bold">Project Profiles</h5>
          <p class="card-text small">
          <?= "[" . $totalPP . "] Total Projects";?>
          <?= $projectCompleted != 0 ? '<br>[' . $projectCompleted . "] Projects completed" : '' ?>
          </p>
        </div>
        <div class="card-footer border-0 pt-0 pb-3 background-color-0">
          <div class="d-flex justify-content-end align-items-start gap-2">
            <a href="projectProfileList" class="btn btn-darker-cus"><i class="bi bi-chevron-right text-white"></i></a>
          </div>
        </div>
      </div>
    </div>

    <div class="col-lg-4 col-md-6 col-sm-12 mb-4">
      <div class="card text-bg-danger h-100" >
        <i class="bi bi-receipt iconbackdrop"></i>
        <div class="card-body">
          <h5 class="card-title fw-bold">Gatepass</h5>
          <p class="card-text small"> <?= "[" . $totalGP . "] Total Gatepass Issued";?>
        </div>
        <div class="card-footer border-0 pt-0 pb-3 background-color-0">
          <div class="d-flex justify-content-end align-items-start gap-2">
            <a href="gatepass" class="btn btn-darker-cus"><i class="bi bi-plus-lg text-white"></i></a>
            <a href="gatepassList" class="btn btn-darker-cus"><i class="bi bi-chevron-right text-white"></i></a>
          </div>
        </div>
      </div>
    </div>
  </div>

  <div class="row" id="chartsrow">
    <div class="col-8">
      <div id="curve_chart" style="width: 100%; height: 400px;"></div>
    </div>
    <div class="col chartContainer">
      <div id="piechart_3d" style="width: 100%; height: 400px;">
      </div>
    </div>
  </div>

  </div>

</div>

<script src="resources/js/chart-loader.js"></script>
<script>
      // Function to check internet connection
      function checkInternetConnection() {
          var online = navigator.onLine;
          if (!online) {
              // showMessage("Loading charts requires active internet connection...", "warning");
              $('chartsrow').innerHTML = "<p class='text-italic alert alert-warning'><i class='bi bi-exclamation-triangle'></i><span class='p-2'>Charts require active internet connection. </span></p>";
              return 0;
          }
          return 1;
      }
      if(checkInternetConnection()){

        google.charts.load('current', {'packages':['corechart']});
        google.charts.setOnLoadCallback(drawChart);
        google.charts.setOnLoadCallback(drawChartCircle);


        function drawChart() {
          var data = google.visualization.arrayToDataTable([
            // ['Year', 'Sales', 'Expenses'],
            // ['2004',  1000,      400],
            // ['2005',  1170,      460],
            // ['2006',  660,       1120],
            // ['2007',  1030,      540]
            ['jtdesc',<?php foreach ($jobtype as $key=>$type): ?>'<?= $type["jtcode"] ?>'<?= ($key+1 == (count(selectQ($conn,"SELECT * FROM tbl_jtype"))) ? '' : ',') ?><?php endforeach; ?><?= '' ?>],

            <?php

            $getjy = selectQ($conn, "CALL get_dynamic_jtype_counts()");
              $outcnt = count($getjy) - 1;
              foreach($getjy as $key=>$value){
                $cnt = count($value) - 1;
                echo "[";
                $count = 0;
                foreach($value as $in=>$data){
                  if($count == 0) echo "'" . fmny($data) . "'";
                  else echo "$data";
                  echo $cnt == $count ? "" : ", ";
                  $count++;
                }
                echo "] ";
                echo $key == $outcnt ? "" : ",";
              }
            ?>
          ]);

          var options = {
            title: 'Past Few Months of Service Requests',
            curveType: 'function',
            chartArea: {
              width: '80%'
            },
            legend: { position: 'bottom' },
          };

          var chart = new google.visualization.LineChart(document.getElementById('curve_chart'));

          chart.draw(data, options);
        }

        function drawChartCircle() {
          var data = google.visualization.arrayToDataTable([
            ['JobType', 'SRQ'],
            <?php foreach ($jobtype as $type): ?>
              ['<?= $type["jtcode"] ?>', <?= count(selectQ($conn, "SELECT *  FROM tbl_register WHERE j_type=?", [$type['jtcode']])) ?>],
            <?php endforeach; ?>
          ]);

          var options = {
            title: 'Overall Service Requests',
            is3D: true,
            chartArea: {
              width: '80%'
            },
            pieSliceText: 'value',
            legend: { 
              position: "bottom",
              labels: {
                <?php foreach ($jobtype as $key=>$type): ?>
                  <?= $key ?>: '<?= $type["jtdesc"] ?>',
                <?php endforeach; ?>
              }
            },
          };

          var chart = new google.visualization.PieChart(document.getElementById('piechart_3d'));
          chart.draw(data, options);
        }

      } 

</script>

<?php
  include 'template/footer.php';
?>