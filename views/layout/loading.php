<div id="loadingContent">
    <div class="spinner-border text-primary h1" role="status">
        <span class="visually-hidden">Loading...</span>
    </div>
    <h3 class="mx-2"> loading</h3>
</div>
<style>
    #loadingContent {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(255, 255, 255, 0.8);
    display: flex;
    justify-content: center;
    align-items: center;
    z-index: 9999;
}

.hide-content {
    display: none !important;
}
</style>
<script>
  setTimeout(function() {
    document.getElementById('loadingContent').classList.add('hide-content');
  }, 1000); 
</script>