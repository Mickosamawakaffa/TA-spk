@extends('layouts.admin')

@section('title', 'Kelola User')

@section('content')
<div class="container-fluid px-4">
    <style>
        .header-users {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: 12px;
            padding: 2rem;
            color: white;
            margin-bottom: 2rem;
            box-shadow: 0 8px 20px rgba(102, 126, 234, 0.15);
        }

        .user-card {
            border: none;
            border-radius: 12px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.08);
            transition: all 0.3s ease;
        }

        .user-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 8px 20px rgba(102, 126, 234, 0.2);
        }

        .badge-role {
            font-size: 0.85rem;
            padding: 0.35rem 0.75rem;
        }

        .badge-admin {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }

        .badge-super-admin {
            background: linear-gradient(135deg, #f5576c 0%, #f093fb 100%);
            color: white;
        }
    </style>

    <!-- Header -->
    <div class="header-users">
        <div class="row align-items-center">
            <div class="col-md-8">
                <h2 class="mb-2">
                    <i class="bi bi-people-fill me-3"></i>Kelola User Administrator
                </h2>
                <p class="mb-0 fs-6">Manage user accounts dan permissions</p>
            </div>
            <div class="col-md-4 text-md-end mt-3 mt-md-0">
                <a href="{{ route('admin.users.create') }}" class="btn btn-light fw-semibold">
                    <i class="bi bi-plus-circle me-2"></i>Tambah User
                </a>
            </div>
        </div>
    </div>

    <!-- Alert -->
    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    <!-- Filter & Search -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body p-4">
            <form action="{{ route('admin.users.index') }}" method="GET" class="row g-3">
                <div class="col-md-6">
                    <label class="form-label fw-semibold mb-2">Cari User</label>
                    <input type="text" name="search" class="form-control" placeholder="Nama atau email..." value="{{ request('search') }}">
                </div>

                <div class="col-md-3">
                    <label class="form-label fw-semibold mb-2">Role</label>
                    <select name="role" class="form-select">
                        <option value="">Semua Role</option>
                        <option value="admin" {{ request('role') == 'admin' ? 'selected' : '' }}>Admin</option>
                        <option value="super_admin" {{ request('role') == 'super_admin' ? 'selected' : '' }}>Super Admin</option>
                    </select>
                </div>

                <div class="col-md-3">
                    <label class="form-label fw-semibold mb-2">Status</label>
                    <select name="status" class="form-select">
                        <option value="">Semua</option>
                        <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Aktif</option>
                        <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Tidak Aktif</option>
                    </select>
                </div>

                <div class="col-12 d-flex gap-2">
                    <button type="submit" class="btn" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white;">
                        <i class="bi bi-search me-2"></i>Cari
                    </button>
                    <a href="{{ route('admin.users.index') }}" class="btn btn-outline-secondary">
                        <i class="bi bi-arrow-clockwise me-2"></i>Reset
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- Users Table -->
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white border-0 py-3">
            <div class="d-flex justify-content-between align-items-center">
                <h5 class="mb-0 fw-semibold">
                    <i class="bi bi-list-ul me-2"></i>Daftar User ({{ $users->total() }})
                </h5>
            </div>
        </div>

        <div class="card-body p-0">
            @if($users->isEmpty())
                <div class="text-center py-5">
                    <i class="bi bi-inbox text-muted" style="font-size: 3rem;"></i>
                    <h5 class="text-muted mt-3">Tidak ada user ditemukan</h5>
                </div>
            @else
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Nama</th>
                            <th>Email</th>
                            <th>Role</th>
                            <th>Status</th>
                            <th>Dibuat</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($users as $user)
                        <tr>
                            <td>
                                <div class="d-flex align-items-center gap-3">
                                    <div class="avatar-sm rounded-circle" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); width: 40px; height: 40px; display: flex; align-items: center; justify-content: center; color: white; font-weight: bold;">
                                        {{ strtoupper(substr($user->name, 0, 1)) }}
                                    </div>
                                    <strong>{{ $user->name }}</strong>
                                </div>
                            </td>
                            <td>
                                <small class="text-muted">{{ $user->email }}</small>
                            </td>
                            <td>
                                @if($user->role === 'super_admin')
                                    <span class="badge badge-super-admin">Super Admin</span>
                                @else
                                    <span class="badge badge-admin">Admin</span>
                                @endif
                            </td>
                            <td>
                                @if($user->deleted_at)
                                    <span class="badge bg-danger">Tidak Aktif</span>
                                @else
                                    <span class="badge bg-success">Aktif</span>
                                @endif
                            </td>
                            <td>
                                <small class="text-muted">{{ $user->created_at->format('d M Y') }}</small>
                            </td>
                            <td>
                                <div class="btn-group btn-group-sm" role="group">
                                    @if($user->deleted_at)
                                        <form action="{{ route('admin.users.restore', $user->id) }}" method="POST" style="display: inline;">
                                            @csrf
                                            <button type="submit" class="btn btn-warning btn-sm" title="Restore">
                                                <i class="bi bi-arrow-counterclockwise"></i>
                                            </button>
                                        </form>
                                    @else
                                        <a href="{{ route('admin.users.edit', $user->id) }}" class="btn btn-primary btn-sm" title="Edit">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                    @endif

                                    @if($user->id !== auth()->id())
                                        <form action="{{ route('admin.users.destroy', $user->id) }}" method="POST" style="display: inline;" onsubmit="return confirm('Yakin hapus user ini?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger btn-sm" title="Hapus">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @endif
        </div>
    </div>

    <!-- Pagination -->
    <div class="d-flex justify-content-center mt-4">
        {{ $users->links() }}
    </div>
</div>
@endsection
