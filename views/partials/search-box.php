<!-- Search Box Partial -->
<div class="search-box">
    <form id="search-form" action="<?php echo $this->url('search'); ?>" method="get" class="d-flex">
        <div class="input-group">
            <input type="text" id="search-input" name="q" class="form-control form-control-sm"
                   placeholder="Rechercher..." aria-label="Rechercher"
                   value="<?php echo isset($_GET['q']) ? $this->escape($_GET['q']) : ''; ?>">
            <button class="btn btn-outline-light btn-sm" type="submit">
                <i class="fas fa-search"></i>
            </button>
        </div>
    </form>
</div>

<style>
    /* Simple search box styles to complement Bootstrap */
    .search-box {
        position: relative;
    }

    .search-box .form-control {
        width: 160px;
        transition: width 0.3s ease;
        background-color: rgba(255, 255, 255, 0.2);
        border-color: rgba(255, 255, 255, 0.2);
        color: white;
    }

    .search-box .form-control:focus,
    .search-focused .form-control {
        width: 220px;
        background-color: white;
        color: #333;
    }

    .search-box .form-control::placeholder {
        color: rgba(255, 255, 255, 0.8);
    }

    .search-box .form-control:focus::placeholder,
    .search-focused .form-control::placeholder {
        color: #6c757d;
    }

    .search-box .btn {
        border-color: rgba(255, 255, 255, 0.2);
    }

    .search-box .btn:hover {
        background-color: rgba(255, 255, 255, 0.1);
    }

    @media (max-width: 767.98px) {
        .search-box .form-control {
            width: 120px;
        }

        .search-box .form-control:focus,
        .search-focused .form-control {
            width: 160px;
        }
    }
</style>