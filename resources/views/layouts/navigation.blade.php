<style>
    .navbar {
        background-color: #000;
        border-bottom: 1px solid #333;
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 10px 16px;
        position: relative;
    }

    .navbar .logo img {
        height: 40px;
        max-width: 200px;
    }

    .nav-links {
        display: flex;
        align-items: center;
        gap: 16px;
    }

    .nav-link {
        text-decoration: none;
        color: #fff;
        font-weight: 500;
    }

    .nav-link:hover {
        color: #facc15;
    }

    .user-dropdown {
        position: relative;
    }

    /* Perbesar area klik ikon */
    .user-dropdown button {
        background: none;
        border: none;
        color: white;
        font-size: 20px;
        cursor: pointer;
        padding: 8px; /* area klik lebih luas */
        border-radius: 6px;
    }

    .user-dropdown button:hover {
        background-color: #111;
    }

    .user-dropdown-menu {
        display: none;
        position: absolute;
        right: 0;
        top: 100%;
        background-color: #000;
        border: 1px solid #333;
        border-radius: 6px;
        margin-top: 0.5rem;
        min-width: 150px;
        z-index: 50;
    }

    .user-dropdown-menu a {
        display: block;
        padding: 10px 14px;
        color: white;
        text-decoration: none;
        font-size: 14px;
    }

    /* Logout kecil & merah */
    .user-dropdown-menu a.logout-link {
        color: red !important;
        font-size: 13px;
        font-weight: bold;
    }

    .user-dropdown-menu a:hover {
        background-color: #111;
    }

    .user-dropdown-menu {
    display: none;
    position: absolute;
    right: 0;
    top: 100%;
    background-color: #000;
    border: 1px solid #333;
    border-radius: 6px;
    margin-top: 0.5rem;
    min-width: 150px;
    z-index: 50;
}

.user-dropdown-menu.show {
    display: block;
}


    .hamburger {
        display: none;
        font-size: 28px;
        background: none;
        border: none;
        color: white;
        cursor: pointer;
    }

    .mobile-menu {
        display: none;
        flex-direction: column;
        background-color: #000;
        padding: 10px 16px;
        border-top: 1px solid #333;
    }

    .mobile-menu a {
        padding: 8px 0;
        color: white;
        text-decoration: none;
        border-bottom: 1px solid #333;
    }

    .mobile-menu a:hover {
        color: #facc15;
    }

    @media (max-width: 768px) {
        .nav-links {
            display: none;
        }

        .hamburger {
            display: block;
        }

        .mobile-menu.show {
            display: flex;
        }

        .navbar .logo img {
            height: 32px;
        }
    }
</style>

<nav class="navbar">
    <!-- Logo -->
    <div class="logo">
        <a href="{{ route('dashboard') }}">
            <img src="{{ asset('images/JARINGAN_PINTAR_NUSANTARA__1_-removebg-preview.png') }}" alt="Logo">
        </a>
    </div>

    <!-- Desktop Menu -->
    <div class="nav-links">
        <a href="{{ route('dashboard') }}" class="nav-link">Dashboard</a>

        @hasanyrole('user|admin')
            <a href="{{ route('tickets.mine') }}" class="nav-link">My Tickets</a>
            <a href="{{ route('tickets.create') }}" class="nav-link">+ Create Ticket</a>
        @endhasanyrole

        @hasanyrole('staff|admin')
            <a href="{{ route('staff.tickets.index') }}" class="nav-link">Manage Tickets</a>
        @endhasanyrole

        @role('staff')
            <a href="{{ route('staff.case_locks.index') }}" class="nav-link">Case Log</a>
        @endrole

        @role('admin')
            <a href="{{ route('admin.case_locks.index') }}" class="nav-link">Case Log</a>
        @endrole


        @role('admin')
            <a href="{{ route('admin.tickets.index') }}" class="nav-link">All Tickets</a>
            <a href="{{ route('admin.clients.index') }}" class="nav-link">Master Client</a>
            <a href="{{ route('admin.telegram.logs.index') }}" class="nav-link">Telegram Log</a>
            <a href="{{ route('wa.qr') }}" class="nav-link">Master Device</a>
            <a href="{{ route('admin.surveys.cs-index') }}" class="nav-link">📊 Survei Pelayanan CS</a>
        @endrole

        <!-- Desktop Profile Dropdown -->
        <div class="user-dropdown">
            <button title="User Menu">⚙️</button>
            <div class="user-dropdown-menu">
                <a href="{{ route('profile.edit') }}">Profile</a>
                <form method="POST" action="{{ route('logout') }}" style="margin:0;">
                    @csrf
                    <a href="{{ route('logout') }}" 
                       class="logout-link"
                       onclick="event.preventDefault(); this.closest('form').submit();">
                       Log Out
                    </a>
                </form>
            </div>
        </div>
    </div>

    <!-- Hamburger -->
    <button class="hamburger" onclick="document.querySelector('.mobile-menu').classList.toggle('show')">
        &#9776;
    </button>
</nav>

<!-- Mobile Menu -->
<div class="mobile-menu">
    <a href="{{ route('dashboard') }}">Dashboard</a>

    @hasanyrole('user|admin')
        <a href="{{ route('tickets.mine') }}">My Tickets</a>
        <a href="{{ route('tickets.create') }}">+ Create Ticket</a>
    @endhasanyrole

    @hasanyrole('staff|admin')
        <a href="{{ route('staff.tickets.index') }}">Manage Tickets</a>
    @endhasanyrole

    @role('staff')
        <a href="{{ route('staff.case_locks.index') }}" class="nav-link">Case Log</a>
    @endrole

    @role('admin')
        <a href="{{ route('admin.case_locks.index') }}" class="nav-link">Case Log</a>
    @endrole


    @role('admin')
        <a href="{{ route('admin.tickets.index') }}">All Tickets</a>
        <a href="{{ route('admin.clients.index') }}">Master Client</a>
        <a href="{{ route('admin.telegram.logs.index') }}">Telegram Log</a>
        <a href="{{ route('wa.qr') }}">Master Device</a>
        <a href="{{ route('admin.surveys.cs-index') }}">📊 Survei Pelayanan CS</a>
    @endrole

    <!-- User Info & Logout (Mobile) -->
    <div style="margin-top: 10px; padding-top: 10px; border-top: 1px solid #333; color: white;">
        <div style="font-weight: bold;">{{ Auth::user()->name }}</div>
        <div style="font-size: 12px; color: #aaa;">{{ Auth::user()->email }}</div>
        <a href="{{ route('profile.edit') }}" style="color: white;">Profile</a>
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <a href="{{ route('logout') }}" 
               onclick="event.preventDefault(); this.closest('form').submit();" 
               style="color: red; font-size: 14px; font-weight: bold;">
               Log Out
            </a>
        </form>
    </div>
</div>


<script>
document.addEventListener('DOMContentLoaded', function () {
    const dropdownBtn = document.querySelector('.user-dropdown button');
    const dropdownMenu = document.querySelector('.user-dropdown-menu');

    dropdownBtn.addEventListener('click', function (e) {
        e.stopPropagation();
        dropdownMenu.classList.toggle('show');
    });

    document.addEventListener('click', function () {
        dropdownMenu.classList.remove('show');
    });
});
</script>
