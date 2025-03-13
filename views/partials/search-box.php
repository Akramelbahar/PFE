<!-- views/partials/search-box.php -->
<form action="<?php echo $this->url('search'); ?>" method="get" class="d-flex">
    <div class="input-group">
        <input type="text" name="q" class="form-control" placeholder="Rechercher..." aria-label="Rechercher" required minlength="3">
        <button class="btn btn-outline-light" type="submit">
            <i class="fas fa-search"></i>
        </button>
    </div>
</form>