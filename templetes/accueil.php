

  <!-- Content Wrapper. Contains page content -->
  <div class="container mt-4">
  <section class="content">
    <!-- Content Wrapper. Contains page content -->
    <div class="container mt-4">
      <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0 text-dark">Page d'acceuil</h1>
            </div><!-- /.col -->
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item"><a href="#"></a></li>
                <li class="breadcrumb-item active"></li>
                </ol>
            </div><!-- /.col -->
            </div><!-- /.row -->
        </div><!-- /.container-fluid -->
      </div>
    
        <div class="container-fluid">
          <!-- Small boxes (Stat box) -->
          <div class="row">
            <!-- Tâches à faire -->
            <div class="col-lg-3 col-md-6 col-12">
              <!-- small box -->
              <div class="small-box bg-info shadow-sm">
                <div class="inner text-center">
                  <h3>150</h3>
                  <p class="text-white">Tâches à faire</p>
                </div>
                <div class="icon">
                  <i class="fas fa-tasks fa-3x"></i>
                </div>
                <a href="{% url 'tasks:taches_faire' %}" class="small-box-footer text-white">Plus d'info <i class="fas fa-arrow-circle-right"></i></a>
              </div>
            </div>
            <!-- ./col -->
    
            <!-- Tâches en cours -->
            <div class="col-lg-3 col-md-6 col-12">
              <!-- small box -->
              <div class="small-box bg-warning shadow-sm">
                <div class="inner text-center">
                  <h3>75</h3>
                  <p class="text-white">Tâches en cours</p>
                </div>
                <div class="icon">
                  <i class="fas fa-spinner fa-3x"></i>
                </div>
                <a href="#" class="small-box-footer text-white">Plus d'info <i class="fas fa-arrow-circle-right"></i></a>
              </div>
            </div>
            <!-- ./col -->
    
            <!-- Tâches terminées -->
            <div class="col-lg-3 col-md-6 col-12">
              <!-- small box -->
              <div class="small-box bg-success shadow-sm">
                <div class="inner text-center">
                  <h3>100</h3>
                  <p class="text-white">Tâches terminées</p>
                </div>
                <div class="icon">
                  <i class="fas fa-check-circle fa-3x"></i>
                </div>
                <a href="#" class="small-box-footer text-white">Plus d'info <i class="fas fa-arrow-circle-right"></i></a>
              </div>
            </div>
            <!-- ./col -->
    
            <!-- Tâches en retard -->
            <div class="col-lg-3 col-md-6 col-12">
              <!-- small box -->
              <div class="small-box bg-danger shadow-sm">
                <div class="inner text-center">
                  <h3>10</h3>
                  <p class="text-white">Tâches en retard</p>
                </div>
                <div class="icon">
                  <i class="fas fa-exclamation-triangle fa-3x"></i>
                </div>
                <a href="#" class="small-box-footer text-white">Plus d'info <i class="fas fa-arrow-circle-right"></i></a>
              </div>
            </div>
            <!-- ./col -->
          </div>
        </div>
    
      <div class="row">
        <div class="col-lg-6">
          <h4>Histogramme de progression des tâches</h4>
          <canvas id="tasksHistogram" height="250"></canvas>
        </div>
        <div class="col-lg-6">
          <h4>Diagramme en camembert des tâches</h4>
          <canvas  id="tasksPieChart" width="250" height="150"></canvas>
        </div>
      </div>
    </section>
  
</div>


<script src="https://kit.fontawesome.com/a076d05399.js"></script>
<script src="https://code.jquery.com/jquery-3.4.1.slim.min.js" integrity="sha384-J6qa4849blE2+poT4WnyKhv5vZF5SrPo0iEjwBvKU7imGFAV0wwj1yYfoRSJoZ+n" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js" integrity="sha384-Q6E9RHvbIyZFJoft+2mJbHaEWldlvI9IOYy5n3zV9zzTtmI3UksdQRVvoxMfooAo" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.4.1/dist/js/bootstrap.min.js" integrity="sha384-wfSDF2E50Y2D1uUdj0O3uMBJnjuUD4Ih7YwaYd1iqfktj0Uod8GCExl3Og8ifwB6" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>