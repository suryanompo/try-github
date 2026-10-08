<x-app-layout title="Manajemen Pengguna & Tim">
    <x-slot:breadcrumb>
        <x-breadcrumb :items="['Pengguna & Tim' => null]" />
    </x-slot:breadcrumb>

    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
        <div>
            <h1 class="text-xl sm:text-2xl font-bold text-slate-900 tracking-tight">Manajemen Pengguna & Tim</h1>
            <p class="text-xs sm:text-sm text-slate-500 mt-1">Kelola hak akses pengguna, role (Admin, Project Manager, Member), dan unit organisasi.</p>
        </div>

        <button
            type="button"
            @click="$dispatch('open-modal', 'modal-add-user')"
            class="inline-flex items-center gap-2 px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-xs sm:text-sm font-semibold rounded-lg shadow-xs transition-colors shrink-0"
        >
            <i data-lucide="user-plus" class="w-4 h-4"></i>
            <span>Tambah Pengguna</span>
        </button>
    </div>

    <!-- Users Table -->
    <div class="bg-white rounded-xl border border-slate-200/80 shadow-2xs overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-xs">
                <thead>
                    <tr class="bg-slate-50/70 border-b border-slate-200/80 text-[11px] font-semibold text-slate-500 uppercase tracking-wider">
                        <th class="py-3 px-5">Pengguna</th>
                        <th class="py-3 px-5">Role Akses</th>
                        <th class="py-3 px-5">Departemen</th>
                        <th class="py-3 px-5">No. Telepon</th>
                        <th class="py-3 px-5 text-center">Proyek Dikelola</th>
                        <th class="py-3 px-5 text-center">Task Ditugaskan</th>
                        <th class="py-3 px-5 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @foreach($users as $u)
                        <tr class="hover:bg-slate-50/60 transition-colors">
                            <td class="py-3.5 px-5">
                                <div class="flex items-center gap-3">
                                    <x-avatar :name="$u->name" size="sm" />
                                    <div>
                                        <span class="font-bold text-slate-900 block">{{ $u->name }}</span>
                                        <span class="text-slate-400 text-[11px]">{{ $u->email }}</span>
                                    </div>
                                </div>
                            </td>
                            <td class="py-3.5 px-5 whitespace-nowrap">
                                <x-badge :type="match($u->role) { 'admin'=>'purple', 'project_manager'=>'info', default=>'neutral' }" size="sm">
                                    {{ $u->role_label }}
                                </x-badge>
                            </td>
                            <td class="py-3.5 px-5 text-slate-600">
                                {{ $u->department ?: '-' }}
                            </td>
                            <td class="py-3.5 px-5 text-slate-500 font-mono">
                                {{ $u->phone ?: '-' }}
                            </td>
                            <td class="py-3.5 px-5 text-center font-bold text-slate-700">
                                {{ $u->managed_projects_count }}
                            </td>
                            <td class="py-3.5 px-5 text-center font-bold text-slate-700">
                                {{ $u->assigned_tasks_count }}
                            </td>
                            <td class="py-3.5 px-5 text-right whitespace-nowrap">
                                <div class="inline-flex items-center gap-1.5">
                                    <button
                                        type="button"
                                        @click="$dispatch('open-modal', 'modal-edit-user-{{ $u->id }}')"
                                        class="p-1.5 text-slate-400 hover:text-amber-600 rounded-md"
                                        title="Ubah"
                                    >
                                        <i data-lucide="edit-3" class="w-4 h-4"></i>
                                    </button>

                                    @if($u->id !== auth()->id())
                                        <form action="{{ route('users.destroy', $u) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus user ini?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="p-1.5 text-slate-400 hover:text-rose-600 rounded-md" title="Hapus">
                                                <i data-lucide="trash-2" class="w-4 h-4"></i>
                                            </button>
                                        </form>
                                    @endif
                                </div>

                                <!-- Modal Edit User -->
                                <x-modal name="modal-edit-user-{{ $u->id }}" title="Ubah Data Pengguna">
                                    <form action="{{ route('users.update', $u) }}" method="POST" class="space-y-4 text-xs text-left">
                                        @csrf
                                        @method('PUT')

                                        <div>
                                            <label class="block font-semibold text-slate-700 mb-1">Nama Lengkap</label>
                                            <input type="text" name="name" value="{{ $u->name }}" required class="w-full bg-white border border-slate-200 rounded-lg px-3 py-2">
                                        </div>

                                        <div>
                                            <label class="block font-semibold text-slate-700 mb-1">Email</label>
                                            <input type="email" name="email" value="{{ $u->email }}" required class="w-full bg-white border border-slate-200 rounded-lg px-3 py-2">
                                        </div>

                                        <div>
                                            <label class="block font-semibold text-slate-700 mb-1">Role Akses</label>
                                            <select name="role" class="w-full bg-white border border-slate-200 rounded-lg px-3 py-2">
                                                <option value="admin" {{ $u->role === 'admin' ? 'selected' : '' }}>Admin (Full Control)</option>
                                                <option value="project_manager" {{ $u->role === 'project_manager' ? 'selected' : '' }}>Project Manager</option>
                                                <option value="member" {{ $u->role === 'member' ? 'selected' : '' }}>Team Member</option>
                                            </select>
                                        </div>

                                        <div>
                                            <label class="block font-semibold text-slate-700 mb-1">Departemen / Unit</label>
                                            <input type="text" name="department" value="{{ $u->department }}" class="w-full bg-white border border-slate-200 rounded-lg px-3 py-2">
                                        </div>

                                        <div>
                                            <label class="block font-semibold text-slate-700 mb-1">No. Telepon</label>
                                            <input type="text" name="phone" value="{{ $u->phone }}" class="w-full bg-white border border-slate-200 rounded-lg px-3 py-2">
                                        </div>

                                        <div>
                                            <label class="block font-semibold text-slate-700 mb-1">Ganti Password (Opsional)</label>
                                            <input type="password" name="password" placeholder="Biarkan kosong jika tidak ingin diubah" class="w-full bg-white border border-slate-200 rounded-lg px-3 py-2">
                                        </div>

                                        <div class="pt-3 border-t border-slate-100 flex items-center justify-end gap-2">
                                            <button type="button" @click="$dispatch('close-modal', 'modal-edit-user-{{ $u->id }}')" class="px-3.5 py-2 border rounded-lg font-semibold text-slate-700">
                                                Batal
                                            </button>
                                            <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-lg font-semibold shadow-xs">
                                                Simpan Perubahan
                                            </button>
                                        </div>
                                    </form>
                                </x-modal>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="p-4 border-t border-slate-100">
            {{ $users->links() }}
        </div>
    </div>

    <!-- Modal Add User -->
    <x-modal name="modal-add-user" title="Tambah Pengguna Baru">
        <form action="{{ route('users.store') }}" method="POST" class="space-y-4 text-xs">
            @csrf

            <div>
                <label class="block font-semibold text-slate-700 mb-1">Nama Lengkap <span class="text-rose-500">*</span></label>
                <input type="text" name="name" required placeholder="Contoh: Hendra Wijaya" class="w-full bg-white border border-slate-200 rounded-lg px-3 py-2">
            </div>

            <div>
                <label class="block font-semibold text-slate-700 mb-1">Alamat Email <span class="text-rose-500">*</span></label>
                <input type="email" name="email" required placeholder="hendra@protrack.id" class="w-full bg-white border border-slate-200 rounded-lg px-3 py-2">
            </div>

            <div>
                <label class="block font-semibold text-slate-700 mb-1">Kata Sandi <span class="text-rose-500">*</span></label>
                <input type="password" name="password" required value="password" class="w-full bg-white border border-slate-200 rounded-lg px-3 py-2">
            </div>

            <div>
                <label class="block font-semibold text-slate-700 mb-1">Role Akses <span class="text-rose-500">*</span></label>
                <select name="role" required class="w-full bg-white border border-slate-200 rounded-lg px-3 py-2">
                    <option value="member" selected>Team Member</option>
                    <option value="project_manager">Project Manager</option>
                    <option value="admin">Admin</option>
                </select>
            </div>

            <div>
                <label class="block font-semibold text-slate-700 mb-1">Departemen / Unit</label>
                <input type="text" name="department" placeholder="Contoh: Frontend Engineering" class="w-full bg-white border border-slate-200 rounded-lg px-3 py-2">
            </div>

            <div>
                <label class="block font-semibold text-slate-700 mb-1">Nomor Telepon</label>
                <input type="text" name="phone" placeholder="081234567890" class="w-full bg-white border border-slate-200 rounded-lg px-3 py-2">
            </div>

            <div class="pt-3 border-t border-slate-100 flex items-center justify-end gap-2">
                <button type="button" @click="$dispatch('close-modal', 'modal-add-user')" class="px-3.5 py-2 border rounded-lg font-semibold text-slate-700">
                    Batal
                </button>
                <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-lg font-semibold shadow-xs">
                    Simpan Pengguna
                </button>
            </div>
        </form>
    </x-modal>
</x-app-layout>
