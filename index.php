<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>🌪️ Cyclone Dashboard</title>
  <link rel="stylesheet" href="master.css">
  <script>
    window.addEventListener("DOMContentLoaded", () => {
      document.querySelectorAll('.dashboard-card').forEach((card, i) => {
        card.style.setProperty('--n', i);
        const btn = card.querySelector('button');
        if (btn) btn.style.setProperty('--n', i);
      });
    });
  </script>

</head>

<body>
  <main>
    <h1>Steven's Weather Stash</h1>
    <div class="dashboard-buttons">
      <div class="dashboard-card pulse" data-tooltip="Live map of tropical systems">
        <img src="img/live_map_preview.jpg" alt="TC Database">
        <button onclick="location.href='tropical_cyclone/tc_index.php'">Cyclone Database</button>
      </div>
      <!-- <div class="dashboard-card pulse" data-tooltip="Live map of tropical systems">
        <img src="img/live_map_preview.jpg" alt="Live Map">
        <button onclick="location.href='live_map.php'">Live Map</button>
      </div>
      <div class="dashboard-card" data-tooltip="Currently active tropical storms">
        <img src="img/storms_preview.jpg" alt="Active Storms">
        <button onclick="location.href='active_storms.php'">Active Storms</button>
      </div>
      <div class="dashboard-card" data-tooltip="Recent cyclone database entries">
        <img src="img/db_preview.jpg" alt="Cyclone DB">
        <button onclick="location.href='recent_cyclones.php'">Cyclone DB</button>
      </div>
      <div class="dashboard-card" data-tooltip="Tornado statistics by Fujita rank">
        <img src="img/tornado_chart_preview.jpg" alt="Tornado Stats">
        <button onclick="location.href='tornado_stats.php'">Tornado Stats</button>
      </div>
      <div class="dashboard-card" data-tooltip="Tornado image archive">
        <img src="img/tornado_gallery.jpg" alt="Tornado Image Gallery">
        <button onclick="location.href='tornado_gallery.php'">Tornado Gallery</button>
      </div>
      <div class="dashboard-card" data-tooltip="Cyclone image archive">
        <img src="img/cyclone_gallery.jpg" alt="Cyclone Image Gallery">
        <button onclick="location.href='cyclone_gallery.php'">Cyclone Gallery</button>
      </div>
      <div class="dashboard-card" data-tooltip="Official NHC forecasts">
        <img src="img/nhc_preview.jpg" alt="NHC Outlook">
        <button onclick="location.href='nhc_outlook.php'">NHC Outlook</button>
      </div>
      <div class="dashboard-card" data-tooltip="CIMSS tropical tracking models">
        <img src="img/cimss_preview.jpg" alt="CIMSS Tracker">
        <button onclick="location.href='cimss_tracker.php'">CIMSS Tracker</button>
      </div>
      <div class="dashboard-card" data-tooltip="Latest GOES satellite imagery">
        <img src="img/goes_preview.jpg" alt="GOES Imagery">
        <button onclick="location.href='goes_imagery.php'">GOES Imagery</button>
      </div>
      <div class="dashboard-card" data-tooltip="Live Himawari satellite feed">
        <img src="img/himawari_preview.jpg" alt="Himawari Imagery">
        <button onclick="location.href='himawari.php'">Himawari Imagery</button>
      </div>
      <div class="dashboard-card" data-tooltip="EUMETSAT European imagery">
        <img src="img/eumetsat_preview.jpg" alt="EUMETSAT Imagery">
        <button onclick="location.href='eumetsat.php'">EUMETSAT Imagery</button>
      </div>
      <div class="dashboard-card" data-tooltip="SPC convective outlooks">
        <img src="img/spc_preview.jpg" alt="SPC Outlook">
        <button onclick="location.href='spc_outlook.php'">SPC Outlook</button>
      </div>
      <div class="dashboard-card" data-tooltip="Lightning strike tracking">
        <img src="img/lightning_preview.jpg" alt="Lightning Tracker">
        <button onclick="location.href='lightning_tracker.php'">Lightning Tracker</button>
      </div>
      <div class="dashboard-card" data-tooltip="Weather model guidance tools">
        <img src="img/model_preview.jpg" alt="Model Guidance">
        <button onclick="location.href='model_guidance.php'">Model Guidance</button>
      </div>
      <div class="dashboard-card" data-tooltip="Reported storm events">
        <img src="img/storm_reports.jpg" alt="Storm Reports">
        <button onclick="location.href='storm_reports.php'">Storm Reports</button>
      </div>
      <div class="dashboard-card" data-tooltip="Current weather watches & warnings">
        <img src="img/watches_warnings.jpg" alt="Watches & Warnings">
        <button onclick="location.href='watches_warnings.php'">Watches & Warnings</button>
      </div>
      <div class="dashboard-card" data-tooltip="Ensemble cyclone forecast paths">
        <img src="img/ensemble_tracks.jpg" alt="Ensemble Tracks">
        <button onclick="location.href='ensemble_tracks.php'">Ensemble Tracks</button>
      </div>
      <div class="dashboard-card" data-tooltip="Historical climatology analysis">
        <img src="img/climatology.jpg" alt="Climatology Maps">
        <button onclick="location.href='climatology.php'">Climatology Maps</button>
      </div>
      <div class="dashboard-card" data-tooltip="Storm surge flooding projections">
        <img src="img/surge_forecast.jpg" alt="Storm Surge Forecast">
        <button onclick="location.href='storm_surge.php'">Storm Surge Forecast</button>
      </div>
      <div class="dashboard-card" data-tooltip="Forecast cones from agencies">
        <img src="img/forecast_cones.jpg" alt="Forecast Cones">
        <button onclick="location.href='forecast_cones.php'">Forecast Cones</button>
      </div>
      <div class="dashboard-card" data-tooltip="Reconnaissance aircraft data">
        <img src="img/aircraft_recon.jpg" alt="Aircraft Recon">
        <button onclick="location.href='aircraft_recon.php'">Aircraft Recon</button>
      </div>
      <div class="dashboard-card" data-tooltip="National flood monitoring maps">
        <img src="img/flood_maps.jpg" alt="Flood Maps">
        <button onclick="location.href='flood_maps.php'">Flood Maps</button>
      </div>
      <div class="dashboard-card" data-tooltip="Real-time satellite loops">
        <img src="img/satellite_loops.jpg" alt="Satellite Loops">
        <button onclick="location.href='satellite_loops.php'">Satellite Loops</button>
      </div>
      <div class="dashboard-card" data-tooltip="Daily precipitation trends">
        <img src="img/precip_analysis.jpg" alt="Precipitation Analysis">
        <button onclick="location.href='precip_analysis.php'">Precipitation Analysis</button>
      </div>
      <div class="dashboard-card" data-tooltip="Upper-level wind shear maps">
        <img src="img/wind_analysis.jpg" alt="Wind Shear Maps">
        <button onclick="location.href='wind_shear.php'">Wind Shear Maps</button>
      </div>
      <div class="dashboard-card" data-tooltip="Tropical weather overview">
        <img src="img/global_tropical.jpg" alt="Global Tropical Activity">
        <button onclick="location.href='global_tropical.php'">Global Tropical Activity</button>
      </div>
      <div class="dashboard-card" data-tooltip="Ocean heat energy mapping">
        <img src="img/ocean_heat.jpg" alt="Ocean Heat Content">
        <button onclick="location.href='ocean_heat.php'">Ocean Heat Content</button>
      </div>
      <div class="dashboard-card" data-tooltip="Upper air analysis graphics">
        <img src="img/upper_air.jpg" alt="Upper Air Analysis">
        <button onclick="location.href='upper_air.php'">Upper Air Analysis</button>
      </div>
      <div class="dashboard-card" data-tooltip="Wave disturbance tracking">
        <img src="img/tropical_waves.jpg" alt="Tropical Waves">
        <button onclick="location.href='tropical_waves.php'">Tropical Waves</button>
      </div>
      <div class="dashboard-card" data-tooltip="Sea surface temperature changes">
        <img src="img/sea_surface_anomaly.jpg" alt="SST Anomalies">
        <button onclick="location.href='sst_anomalies.php'">SST Anomalies</button>
      </div>
      <div class="dashboard-card" data-tooltip="Heat index mapping">
        <img src="img/heat_index.jpg" alt="Heat Index Maps">
        <button onclick="location.href='heat_index.php'">Heat Index Maps</button>
      </div>
      <div class="dashboard-card" data-tooltip="Visibility forecast maps">
        <img src="img/visibility_map.jpg" alt="Visibility Maps">
        <button onclick="location.href='visibility.php'">Visibility Maps</button>
      </div>
      <div class="dashboard-card" data-tooltip="Fire weather risk outlook">
        <img src="img/fire_weather.jpg" alt="Fire Weather Outlook">
        <button onclick="location.href='fire_weather.php'">Fire Weather Outlook</button>
      </div> -->
    </div>
    <div class="form-action-bar">
      <button onclick="window.location.href='tc_admin.php'">Manage Cyclones</button>
      <button class="logout-btn" onclick="window.location.href='logout.php'">Logout</button>
    </div>
  </main>
</body>

</html>