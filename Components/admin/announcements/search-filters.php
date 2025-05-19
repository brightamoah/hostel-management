<div class="d-flex flex-column flex-md-row gap-3 py-3">
    <div class="search-bar flex-fill">
        <div class="input-group align-items-center" style="border-radius: 2rem; border: 1px solid #e3e6ed;">
            <span class="input-group-text bg-transparent border-0 px-3" id="search-icon">
                <i class="fa fa-search text-primary"></i>
            </span>
            <input type="text" id="searchInput" class="form-control border-0 bg-transparent px-2" placeholder="Search announcements..." aria-label="Search" aria-describedby="search-icon" style="background: transparent; box-shadow: none; font-size: 1rem;">
        </div>
    </div>
    <div class="d-flex gap-2 flex-wrap">
        <div class="dropdown">
            <button class="btn btn-outline-secondary dropdown-toggle" type="button" id="priorityFilter" data-bs-toggle="dropdown" aria-expanded="false">
                <i class="bx bx-filter-alt me-1"></i>Priority
            </button>
            <ul class="dropdown-menu" aria-labelledby="priorityFilter">
                <li><a class="dropdown-item filter-priority active" href="javascript:void(0);" data-value="all">All Priorities</a></li>
                <li><a class="dropdown-item filter-priority" href="javascript:void(0);" data-value="Urgent">Urgent</a></li>
                <li><a class="dropdown-item filter-priority" href="javascript:void(0);" data-value="High">High</a></li>
                <li><a class="dropdown-item filter-priority" href="javascript:void(0);" data-value="Medium">Medium</a></li>
                <li><a class="dropdown-item filter-priority" href="javascript:void(0);" data-value="Low">Low</a></li>
            </ul>
        </div>
        <div class="dropdown">
            <button class="btn btn-outline-secondary dropdown-toggle" type="button" id="audienceFilter" data-bs-toggle="dropdown" aria-expanded="false">
                <i class="bx bx-group me-1"></i>Audience
            </button>
            <ul class="dropdown-menu" aria-labelledby="audienceFilter">
                <li><a class="dropdown-item filter-audience active" href="javascript:void(0);" data-value="all">All Audiences</a></li>
                <li><a class="dropdown-item filter-audience" href="javascript:void(0);" data-value="Students">Students</a></li>
                <li><a class="dropdown-item filter-audience" href="javascript:void(0);" data-value="Admins">Admins</a></li>
                <li><a class="dropdown-item filter-audience" href="javascript:void(0);" data-value="All">Everyone</a></li>
                <li><a class="dropdown-item filter-audience" href="javascript:void(0);" data-value="Specific">Specific Groups</a></li>
            </ul>
        </div>
        <div class="dropdown">
            <button class="btn btn-outline-secondary dropdown-toggle" type="button" id="sortOrder" data-bs-toggle="dropdown" aria-expanded="false">
                <i class="bx bx-sort me-1"></i>Sort
            </button>
            <ul class="dropdown-menu" aria-labelledby="sortOrder">
                <li><a class="dropdown-item sort-option active" href="javascript:void(0);" data-sort="newest">Newest First</a></li>
                <li><a class="dropdown-item sort-option" href="javascript:void(0);" data-sort="oldest">Oldest First</a></li>
                <li><a class="dropdown-item sort-option" href="javascript:void(0);" data-sort="priority">Priority (High to Low)</a></li>
                <li><a class="dropdown-item sort-option" href="javascript:void(0);" data-sort="title">Title (A-Z)</a></li>
            </ul>
        </div>
    </div>
</div>