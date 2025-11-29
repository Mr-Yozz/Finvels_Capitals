<aside class="sidebar">
    <ul class="sidebar-menu">
        <li><a href="{{route('dashboard')}}" class="sidebar-link"><i class="fa-solid fa-gauge"></i><span>Dashboard</span></a></li>
        <!-- <li class="sidebar-item dropdown">
            <a href="{{route('loans.groups')}}" class="sidebar-link " data-bs-toggle="dropdown" aria-expanded="false">
                <i class="fa-solid fa-building"></i> Organization
            </a>
            <ul class="dropdown-menu">
                <li><a class="dropdown-item" href="{{ route('branches.index') }}"><i class="fa-solid fa-code-branch me-2"></i>Branches</a></li>
                <li><a class="dropdown-item" href="{{ route('groups.index') }}"><i class="fa-solid fa-users me-2"></i>Groups</a></li>
                <li><a class="dropdown-item" href="{{ route('members.index') }}"><i class="fa-solid fa-user me-2"></i>Members</a></li>
            </ul>
        </li> -->
        <li><a href="{{route('branches.index')}}" class="sidebar-link"><i class="fa-solid fa-building"></i>Organization</a></li>
        <!--<li><a href="{{route('groups.index')}}" class="sidebar-link"><i class="fa-solid fa-users"></i>Groups</a></li>
        <li><a href="{{route('members.index')}}" class="sidebar-link"><i class="fa-solid fa-user"></i>Members</a></li> -->
        <!-- <li><a href="{{route('loans.index')}}" class="sidebar-link"><i class="fa-solid fa-hand-holding-dollar"></i>Loans</a></li> -->
        <li><a href="{{route('loans.groups')}}" class="sidebar-link"><i class="fa-solid fa-hand-holding-dollar"></i>Loans</a></li>
        <li>
            <a href="{{route('repayments.index')}}" class="sidebar-link"><i class="fa-solid fa-rotate-right"></i>
                Repayments
            </a>
        </li>
        <!-- <li><a href="repay_entry.php" class="sidebar-link"><i class="fa-solid fa-plus-circle"></i>Apply Repayment</a></li> -->
        <li><a href="{{route('reports.daily')}}" class="sidebar-link"><i class="fa-solid fa-calendar-day"></i>Daily Billing</a></li>
        <li><a href="{{route('expenses.index')}}" class="sidebar-link"><i class="fa-solid fa-file-invoice-dollar"></i>Accounts / Expenses</a></li>
        <li><a href="{{ route('account.categories')}}" class="sidebar-link"><i class="fa-solid fa-list"></i>Expense Categories</a></li>
        <!-- <li><a href="export_loans.php" class="sidebar-link"><i class="fa-solid fa-file-export"></i>Export CSV</a></li> -->
    </ul>
</aside>