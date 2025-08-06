<link rel="icon" type="image/png" href="../assets/images/icon.png" />
<link href="../assets/bootstrap-5.3.6/css/bootstrap.min.css" rel="stylesheet">
<link href="../assets/fontawesome/css/all.min.css" rel="stylesheet">
<link href="../assets/adminkit/static/css/app.css" rel="stylesheet">
<link href="../assets/datatables/datatables.css" rel="stylesheet">
<style>
  #toast-container {
    position: fixed !important;
    bottom: 1rem;
    right: 1rem;
    left: auto !important;
    z-index: 9999;
  }
  .fa-beat, .fa-bounce, .fa-fade, .fa-beat-fade, .fa-flip, .fa-pulse, .fa-shake, .fa-spin, .fa-spin-pulse
{
  animation-duration: 2s;
  animation-iteration-count: infinite;
}
.table-wrapper {
            overflow-x: auto; /* Enable horizontal scrolling */
            -webkit-overflow-scrolling: touch; /* Smooth scrolling for mobile */
        }
        table th, table td {
    text-align: left !important;
}
.blinking {
  animation: blink 1s infinite;
}

@keyframes blink {
  0%, 100% { opacity: 1; }
  50% { opacity: 0; }
}
li.sidebar-item.submenu > a.sidebar-link {
  background: linear-gradient(to left, #222e3c, #3a4d63) !important;
  border-bottom:2px solid #222e3c;
}
</style>
<!--<style>
#soal.sidebar-dropdown a {
    background-color: rgba(0, 0, 0, 0.15); /* Warna gelap dengan transparansi */
    padding: 10px 25px;
    margin-top: -1px; /* Untuk menghilangkan gap */
}
</style>-->
