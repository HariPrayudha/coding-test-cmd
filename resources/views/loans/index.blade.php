<x-app-layout>
    <x-slot name="title">Daftar Pengajuan Pembiayaan</x-slot>

    @php
        $isAnalyst = auth()->check() ? auth()->user()->isAnalyst() : true;
    @endphp

    <div x-data="loanPortalApp()" x-cloak class="space-y-6 sm:space-y-8">

        <!-- Top Header & Actions -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight">
                    Daftar Pengajuan Pembiayaan
                </h1>
                <p class="text-xs sm:text-sm text-slate-500 mt-1">
                    Kelola dan tinjau berkas pengajuan kredit nasabah kendaraan dan multiguna PT Capella Multidana.
                </p>
            </div>

            @if (!$isAnalyst)
                <div class="flex items-center gap-3">
                    <x-button variant="primary" size="md" @click="openCreateModal()">
                        <svg class="w-5 h-5 text-[#0B132B]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"></path>
                        </svg>
                        <span>Catat Pengajuan Baru</span>
                    </x-button>
                </div>
            @endif
        </div>

        <!-- Statistical KPI Cards (Responsive 2 cols on mobile, 4 on desktop) -->
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4">
            <x-stat-card 
                title="Total Pengajuan" 
                :value="$stats['total']"
                subtitle="Semua data masuk"
                variant="default">
                <x-slot name="icon">
                    <svg class="w-5 h-5 sm:w-6 sm:h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                </x-slot>
            </x-stat-card>

            <x-stat-card 
                title="Menunggu" 
                :value="$stats['pending']"
                subtitle="Perlu diverifikasi"
                variant="amber">
                <x-slot name="icon">
                    <svg class="w-5 h-5 sm:w-6 sm:h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </x-slot>
            </x-stat-card>

            <x-stat-card 
                title="Disetujui" 
                :value="$stats['approved']"
                :subtitle="'Nominal: ' . \App\Models\LoanApplication::formatRupiah($stats['total_approved_amount'])"
                variant="emerald">
                <x-slot name="icon">
                    <svg class="w-5 h-5 sm:w-6 sm:h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </x-slot>
            </x-stat-card>

            <x-stat-card 
                title="Ditolak" 
                :value="$stats['rejected']"
                subtitle="Tidak memenuhi syarat"
                variant="rose">
                <x-slot name="icon">
                    <svg class="w-5 h-5 sm:w-6 sm:h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </x-slot>
            </x-stat-card>
        </div>

        <!-- Filter & Search Section with Custom Dropdowns and Live Animation -->
        <div class="bg-white rounded-2xl p-4 sm:p-5 border border-slate-200/80 shadow-xs">
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-3 sm:gap-4">
                <!-- Live Search Input -->
                <div class="relative flex-1">
                    <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                        <svg class="w-4 h-4 sm:w-5 sm:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                    </div>
                    <input type="text" 
                           x-model="searchQuery" 
                           placeholder="Ketik untuk mencari nama lengkap nasabah..." 
                           class="w-full pl-10 pr-4 py-2.5 rounded-xl border border-slate-200 bg-slate-50/50 text-xs sm:text-sm placeholder:text-slate-400 focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#E59E15]/40 focus:border-[#E59E15] transition">
                </div>

                <!-- Custom Styled Dropdown Filters -->
                <div class="grid grid-cols-2 sm:flex sm:items-center gap-2.5 sm:gap-3">
                    
                    <!-- 1. Custom Status Dropdown -->
                    <div class="relative w-full sm:w-44" @click.outside="statusDropdownOpen = false">
                        <button @click="statusDropdownOpen = !statusDropdownOpen"
                                type="button"
                                class="w-full flex items-center justify-between pl-3.5 pr-3 py-2.5 rounded-xl border border-slate-200 bg-slate-50/70 hover:bg-white text-xs sm:text-sm font-medium text-slate-700 focus:outline-none focus:ring-2 focus:ring-[#E59E15]/40 focus:border-[#E59E15] transition shadow-2xs cursor-pointer">
                            <span class="truncate" x-text="selectedStatusLabel"></span>
                            <svg class="w-4 h-4 text-slate-400 transition-transform duration-200 shrink-0 ml-1.5"
                                 :class="statusDropdownOpen ? 'rotate-180 text-amber-600' : ''"
                                 fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </button>

                        <!-- Floating Options Menu -->
                        <div x-show="statusDropdownOpen"
                             x-transition:enter="transition ease-out duration-150 transform"
                             x-transition:enter-start="opacity-0 scale-95 -translate-y-1"
                             x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                             x-transition:leave="transition ease-in duration-100 transform"
                             x-transition:leave-start="opacity-100 scale-100 translate-y-0"
                             x-transition:leave-end="opacity-0 scale-95 -translate-y-1"
                             class="absolute z-30 mt-1.5 w-full bg-white rounded-xl shadow-xl border border-slate-200/90 py-1.5 focus:outline-none overflow-hidden"
                             style="display: none;">
                            <button @click="setStatusFilter('all', 'Semua Status')"
                                    type="button"
                                    class="w-full text-left px-3.5 py-2 text-xs font-medium flex items-center justify-between transition hover:bg-slate-50 cursor-pointer"
                                    :class="statusFilter === 'all' ? 'text-amber-700 bg-amber-50/50 font-bold' : 'text-slate-700'">
                                <span>Semua Status</span>
                                <svg x-show="statusFilter === 'all'" class="w-4 h-4 text-amber-600 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                                </svg>
                            </button>
                            @foreach ($loanStatuses as $status)
                            <button @click="setStatusFilter('{{ $status->value }}', '{{ $status->label() }}')"
                                    type="button"
                                    class="w-full text-left px-3.5 py-2 text-xs font-medium flex items-center justify-between transition hover:bg-slate-50 cursor-pointer"
                                    :class="statusFilter === '{{ $status->value }}' ? 'text-amber-700 bg-amber-50/50 font-bold' : 'text-slate-700'">
                                <span class="flex items-center gap-2">
                                    <span class="w-2 h-2 rounded-full {{ $status->value === 'approved' ? 'bg-emerald-500' : ($status->value === 'rejected' ? 'bg-rose-500' : 'bg-amber-500') }}"></span>
                                    <span>{{ $status->label() }}</span>
                                </span>
                                <svg x-show="statusFilter === '{{ $status->value }}'" class="w-4 h-4 text-amber-600 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                                </svg>
                            </button>
                            @endforeach
                        </div>
                    </div>

                    <!-- 2. Custom Type Dropdown -->
                    <div class="relative w-full sm:w-44" @click.outside="typeDropdownOpen = false">
                        <button @click="typeDropdownOpen = !typeDropdownOpen"
                                type="button"
                                class="w-full flex items-center justify-between pl-3.5 pr-3 py-2.5 rounded-xl border border-slate-200 bg-slate-50/70 hover:bg-white text-xs sm:text-sm font-medium text-slate-700 focus:outline-none focus:ring-2 focus:ring-[#E59E15]/40 focus:border-[#E59E15] transition shadow-2xs cursor-pointer">
                            <span class="truncate" x-text="selectedTypeLabel"></span>
                            <svg class="w-4 h-4 text-slate-400 transition-transform duration-200 shrink-0 ml-1.5"
                                 :class="typeDropdownOpen ? 'rotate-180 text-amber-600' : ''"
                                 fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </button>

                        <!-- Floating Options Menu -->
                        <div x-show="typeDropdownOpen"
                             x-transition:enter="transition ease-out duration-150 transform"
                             x-transition:enter-start="opacity-0 scale-95 -translate-y-1"
                             x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                             x-transition:leave="transition ease-in duration-100 transform"
                             x-transition:leave-start="opacity-100 scale-100 translate-y-0"
                             x-transition:leave-end="opacity-0 scale-95 -translate-y-1"
                             class="absolute z-30 mt-1.5 w-full bg-white rounded-xl shadow-xl border border-slate-200/90 py-1.5 focus:outline-none overflow-hidden"
                             style="display: none;">
                            <button @click="setTypeFilter('all', 'Semua Tipe')"
                                    type="button"
                                    class="w-full text-left px-3.5 py-2 text-xs font-medium flex items-center justify-between transition hover:bg-slate-50 cursor-pointer"
                                    :class="typeFilter === 'all' ? 'text-amber-700 bg-amber-50/50 font-bold' : 'text-slate-700'">
                                <span>Semua Tipe</span>
                                <svg x-show="typeFilter === 'all'" class="w-4 h-4 text-amber-600 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                                </svg>
                            </button>
                            @foreach ($loanTypes as $type)
                            <button @click="setTypeFilter('{{ $type->value }}', '{{ $type->label() }}')"
                                    type="button"
                                    class="w-full text-left px-3.5 py-2 text-xs font-medium flex items-center justify-between transition hover:bg-slate-50 cursor-pointer"
                                    :class="typeFilter === '{{ $type->value }}' ? 'text-amber-700 bg-amber-50/50 font-bold' : 'text-slate-700'">
                                <span>{{ $type->label() }}</span>
                                <svg x-show="typeFilter === '{{ $type->value }}'" class="w-4 h-4 text-amber-600 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                                </svg>
                            </button>
                            @endforeach
                        </div>
                    </div>

                    <!-- Reset Filter Button (Using x-button component) -->
                    <x-button x-show="searchQuery !== '' || statusFilter !== 'all' || typeFilter !== 'all'" 
                              @click="resetFilters()" 
                              variant="secondary"
                              size="sm"
                              class="col-span-2 sm:col-span-1">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                        <span>Reset</span>
                    </x-button>
                </div>
            </div>
        </div>

        <!-- Tabel Pengajuan (Ringkas, Tanpa Avatar Inisial, Aksi Icon-Only Center, Animated Rows) -->
        <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm text-slate-600">
                    <thead class="bg-slate-50 border-b border-slate-200 text-[11px] uppercase font-bold text-slate-600 tracking-wider">
                        <tr>
                            <th scope="col" class="py-3.5 px-5">Nama Lengkap</th>
                            <th scope="col" class="py-3.5 px-4">Tipe</th>
                            <th scope="col" class="py-3.5 px-4 text-right">Nominal</th>
                            <th scope="col" class="py-3.5 px-4 text-center">Tenor</th>
                            <th scope="col" class="py-3.5 px-4 text-right">Tagihan/Bln</th>
                            <th scope="col" class="py-3.5 px-4">Tanggal</th>
                            <th scope="col" class="py-3.5 px-4 text-center">Status</th>
                            @if ($isAnalyst)
                                <th scope="col" class="py-3.5 px-4 text-center">Aksi</th>
                            @endif
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 font-normal text-xs sm:text-sm">
                        <template x-for="loan in allLoans" :key="loan.id">
                            <tr x-show="matchesFilter(loan)"
                                x-transition:enter="transition ease-out duration-250 transform"
                                x-transition:enter-start="opacity-0 -translate-y-2 scale-98"
                                x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                                x-transition:leave="transition ease-in duration-150 transform"
                                x-transition:leave-start="opacity-100 translate-y-0 scale-100"
                                x-transition:leave-end="opacity-0 -translate-y-2 scale-98"
                                class="hover:bg-slate-50/70 transition-colors">
                                <!-- 1. Nama Lengkap (Tanpa inisial avatar) -->
                                <td class="py-3.5 px-5 font-semibold text-slate-900">
                                    <span class="block leading-tight text-slate-900 text-sm" x-text="loan.customer_name"></span>
                                    <span class="block text-[11px] font-normal text-slate-400 mt-0.5" x-text="'Gaji: ' + loan.formatted_income"></span>
                                </td>

                                <!-- 2. Tipe -->
                                <td class="py-3.5 px-4 whitespace-nowrap">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-md text-[11px] font-semibold"
                                          :class="loan.loan_type === 'motor' ? 'bg-blue-50 text-blue-700 border border-blue-200/60' : (loan.loan_type === 'mobil' ? 'bg-indigo-50 text-indigo-700 border border-indigo-200/60' : 'bg-amber-50 text-amber-800 border border-amber-200/60')"
                                          x-text="loan.type_label">
                                    </span>
                                </td>

                                <!-- 3. Nominal -->
                                <td class="py-3.5 px-4 whitespace-nowrap text-right font-bold text-slate-900" x-text="loan.formatted_amount"></td>

                                <!-- 4. Tenor -->
                                <td class="py-3.5 px-4 whitespace-nowrap text-center">
                                    <span class="px-2 py-0.5 rounded-md bg-slate-100 text-slate-700 font-medium text-xs" x-text="loan.tenor_months + ' Bln'"></span>
                                </td>

                                <!-- 5. Tagihan/Bln -->
                                <td class="py-3.5 px-4 whitespace-nowrap text-right font-semibold text-amber-800" x-text="loan.formatted_installment"></td>

                                <!-- 6. Tanggal -->
                                <td class="py-3.5 px-4 whitespace-nowrap text-xs text-slate-500" x-text="loan.formatted_date"></td>

                                <!-- 7. Status -->
                                <td class="py-3.5 px-4 whitespace-nowrap text-center">
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[11px] font-semibold ring-1 ring-inset"
                                          :class="loan.status === 'approved' ? 'bg-emerald-50 text-emerald-700 ring-emerald-600/20' : (loan.status === 'rejected' ? 'bg-rose-50 text-rose-700 ring-rose-600/20' : 'bg-amber-50 text-amber-800 ring-amber-600/20')">
                                        <span class="w-1.5 h-1.5 rounded-full" 
                                              :class="loan.status === 'approved' ? 'bg-emerald-500' : (loan.status === 'rejected' ? 'bg-rose-500' : 'bg-amber-500 animate-pulse')"></span>
                                        <span x-text="loan.status_label"></span>
                                    </span>
                                </td>

                                <!-- 8. Aksi (Hanya untuk Analyst / Approver, HANYA ICON & RATA TENGAH) -->
                                @if ($isAnalyst)
                                    <td class="py-3.5 px-4 whitespace-nowrap text-center">
                                        <div class="flex items-center justify-center gap-1.5">
                                            <!-- Tombol Detail (Icon Mata) -->
                                            <x-button variant="secondary" size="icon" @click="openDetailModal(loan.id)" title="Lihat Detail & Kalkulasi">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                                </svg>
                                            </x-button>

                                            <!-- Tombol Setujui (Icon Checkmark - Crisp & Unclipped) -->
                                            <x-button x-show="loan.status === 'pending'"
                                                      variant="emerald"
                                                      size="icon"
                                                      @click="confirmApprove(loan.id, loan.customer_name, loan.formatted_amount)" 
                                                      title="Setujui Pengajuan">
                                                <svg class="w-4 h-4 text-white" viewBox="0 0 20 20" fill="currentColor">
                                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                                                </svg>
                                            </x-button>

                                            <!-- Tombol Tolak (Icon X) -->
                                            <x-button x-show="loan.status === 'pending'"
                                                      variant="rose-soft"
                                                      size="icon"
                                                      @click="confirmReject(loan.id, loan.customer_name)" 
                                                      title="Tolak Pengajuan">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"></path>
                                                </svg>
                                            </x-button>
                                        </div>
                                    </td>
                                @endif
                            </tr>
                        </template>

                        <!-- Empty State with Fade Transition -->
                        <tr x-show="visibleCount === 0"
                            x-transition:enter="transition ease-out duration-300"
                            x-transition:enter-start="opacity-0"
                            x-transition:enter-end="opacity-100">
                            <td colspan="{{ $isAnalyst ? 8 : 7 }}" class="py-12 text-center text-slate-500">
                                <div class="max-w-sm mx-auto flex flex-col items-center">
                                    <div class="w-12 h-12 rounded-2xl bg-amber-50 text-amber-600 flex items-center justify-center mb-3">
                                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                        </svg>
                                    </div>
                                    <h3 class="text-sm font-bold text-slate-800">Tidak ada data ditemukan</h3>
                                    <p class="text-xs text-slate-400 mt-1">
                                        Tidak ada berkas yang sesuai dengan kata kunci atau filter yang dipilih.
                                    </p>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- ========================================== -->
        <!-- MODAL 1: FORM PENGAJUAN BARU (MENGGUNAKAN KOMPONEN x-modal & x-button) -->
        <!-- Dilengkapi Custom Styled Dropdown Tenor -->
        <!-- ========================================== -->
        <x-modal show="createModalOpen" 
                 onClose="closeCreateModal()" 
                 title="Formulir Pengajuan Pembiayaan" 
                 subtitle="Input data pengajuan nasabah baru PT Capella Multidana" 
                 maxWidth="2xl">
            
            <x-slot name="headerIcon">
                <div class="w-9 h-9 rounded-xl bg-amber-500/20 border border-amber-500/30 flex items-center justify-center text-[#E59E15]">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                    </svg>
                </div>
            </x-slot>

            <form id="createLoanForm" action="{{ route('loans.store') }}" method="POST" @submit.prevent="validateAndSubmit()">
                @csrf

                <div class="p-6 space-y-4 sm:space-y-5">
                    <!-- Field a) Nama Lengkap Nasabah -->
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">
                            Nama Lengkap Nasabah <span class="text-rose-500">*</span>
                        </label>
                        <input type="text" 
                               name="customer_name" 
                               x-model="formData.customer_name"
                               required 
                               placeholder="Nama lengkap sesuai KTP"
                               class="w-full px-4 py-2.5 rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-[#E59E15]/40 focus:border-[#E59E15] text-sm transition">
                    </div>

                    <!-- Field b) Tipe Pengajuan (Radio Card) -->
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">
                            Tipe Pengajuan <span class="text-rose-500">*</span>
                        </label>
                        <div class="grid grid-cols-3 gap-2.5 sm:gap-3">
                            @foreach ($loanTypes as $type)
                                <label class="relative flex flex-col items-center justify-center p-3 rounded-xl border-2 cursor-pointer transition text-center"
                                       :class="formData.loan_type === '{{ $type->value }}' ? 'border-[#E59E15] bg-amber-50/50 text-slate-900 font-bold' : 'border-slate-200 hover:border-slate-300 text-slate-600'">
                                    <input type="radio" 
                                           name="loan_type" 
                                           value="{{ $type->value }}" 
                                           x-model="formData.loan_type" 
                                           class="sr-only">
                                    <span class="text-xs">{{ $type->label() }}</span>
                                </label>
                            @endforeach
                        </div>
                    </div>

                    <!-- Grid 2 Kolom: Nominal Pengajuan & Tenor -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <!-- Field c) Nominal Pengajuan -->
                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">
                                Nominal Pengajuan (Rp) <span class="text-rose-500">*</span>
                            </label>
                            <div class="relative">
                                <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center text-xs font-bold text-slate-400">Rp</span>
                                <input type="text" 
                                       name="loan_amount" 
                                       x-model="formattedAmount" 
                                       @input="updateAmount($event.target.value)"
                                       required 
                                       placeholder="0"
                                       class="w-full pl-10 pr-4 py-2.5 rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-[#E59E15]/40 focus:border-[#E59E15] text-sm font-semibold text-slate-900 transition">
                            </div>
                        </div>

                        <!-- Field d) CUSTOM STYLED DROPDOWN TENOR LENGKAP (1, 3, 6, 9, 12, 18, 24 Bulan) -->
                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">
                                Tenor (Bulan) <span class="text-rose-500">*</span>
                            </label>
                            <input type="hidden" name="tenor_months" :value="formData.tenor_months">
                            <div class="relative" @click.outside="tenorDropdownOpen = false">
                                <button @click="tenorDropdownOpen = !tenorDropdownOpen"
                                        type="button"
                                        class="w-full flex items-center justify-between pl-3.5 pr-3 py-2.5 rounded-xl border border-slate-200 bg-white hover:bg-slate-50 text-xs sm:text-sm font-semibold text-slate-800 focus:outline-none focus:ring-2 focus:ring-[#E59E15]/40 focus:border-[#E59E15] transition shadow-2xs cursor-pointer">
                                    <span class="truncate" x-text="selectedTenorLabel"></span>
                                    <svg class="w-4 h-4 text-slate-400 transition-transform duration-200 shrink-0 ml-1.5"
                                         :class="tenorDropdownOpen ? 'rotate-180 text-amber-600' : ''"
                                         fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                    </svg>
                                </button>

                                <!-- Floating Options Menu -->
                                <div x-show="tenorDropdownOpen"
                                     x-transition:enter="transition ease-out duration-150 transform"
                                     x-transition:enter-start="opacity-0 scale-95 -translate-y-1"
                                     x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                                     x-transition:leave="transition ease-in duration-100 transform"
                                     x-transition:leave-start="opacity-100 scale-100 translate-y-0"
                                     x-transition:leave-end="opacity-0 scale-95 -translate-y-1"
                                     class="absolute z-30 mt-1.5 w-full bg-white rounded-xl shadow-xl border border-slate-200/90 py-1.5 focus:outline-none max-h-48 overflow-y-auto"
                                     style="display: none;">
                                    @php
                                        $tenorOptions = [
                                            1 => '1 Bulan',
                                            3 => '3 Bulan',
                                            6 => '6 Bulan',
                                            9 => '9 Bulan',
                                            12 => '12 Bulan (1 Tahun)',
                                            18 => '18 Bulan (1.5 Tahun)',
                                            24 => '24 Bulan (2 Tahun)',
                                        ];
                                    @endphp
                                    @foreach ($tenorOptions as $m => $label)
                                        <button @click="setTenor({{ $m }}, '{{ $label }}')"
                                                type="button"
                                                class="w-full text-left px-3.5 py-2 text-xs font-medium flex items-center justify-between transition hover:bg-slate-50 cursor-pointer"
                                                :class="formData.tenor_months === {{ $m }} ? 'text-amber-800 bg-amber-50/50 font-bold' : 'text-slate-700'">
                                            <span>{{ $label }}</span>
                                            <svg x-show="formData.tenor_months === {{ $m }}" class="w-4 h-4 text-amber-600 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                                            </svg>
                                        </button>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Field e) Pendapatan Bulanan Nasabah -->
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">
                            Pendapatan Bulanan Nasabah (Rp) <span class="text-rose-500">*</span>
                        </label>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center text-xs font-bold text-slate-400">Rp</span>
                            <input type="text" 
                                   name="monthly_income" 
                                   x-model="formattedIncome" 
                                   @input="updateIncome($event.target.value)"
                                   required 
                                   placeholder="0"
                                   class="w-full pl-10 pr-4 py-2.5 rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-[#E59E15]/40 focus:border-[#E59E15] text-sm font-semibold text-slate-900 transition">
                        </div>
                    </div>

                    <!-- Field f) Catatan -->
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">
                            Catatan Tambahan (Opsional)
                        </label>
                        <textarea name="notes" 
                                  x-model="formData.notes"
                                  rows="2" 
                                  placeholder="Keterangan jaminan, keperluan kredit, atau kontak penjamin..."
                                  class="w-full px-4 py-2.5 rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-[#E59E15]/40 focus:border-[#E59E15] text-xs sm:text-sm transition"></textarea>
                    </div>

                    <!-- Live Calculation Simulation Card with Animation -->
                    <div x-show="rawAmount > 0" 
                         x-transition:enter="transition ease-out duration-200 transform"
                         x-transition:enter-start="opacity-0 translate-y-2"
                         x-transition:enter-end="opacity-100 translate-y-0"
                         class="p-4 rounded-2xl bg-amber-50/70 border border-amber-200/80 space-y-2">
                        <div class="flex items-center justify-between">
                            <span class="text-xs font-bold text-amber-900 uppercase tracking-wider">Simulasi Tagihan Bulanan</span>
                            <span class="text-[11px] font-semibold text-amber-800 bg-amber-100/70 px-2 py-0.5 rounded-md">Bunga Flat 0.9%/bln</span>
                        </div>
                        <div class="flex items-baseline justify-between pt-1">
                            <span class="text-xs text-slate-600">Estimasi Cicilan:</span>
                            <span class="text-lg font-extrabold text-slate-900" x-text="liveCalculation.formattedMonthly">Rp 0</span>
                        </div>
                        <div class="flex items-center justify-between text-xs text-slate-500 border-t border-amber-200/50 pt-2">
                            <span>Pokok: <strong class="text-slate-800" x-text="liveCalculation.formattedPrincipal">Rp 0</strong></span>
                            <span>Margin: <strong class="text-slate-800" x-text="liveCalculation.formattedInterest">Rp 0</strong></span>
                            <span>Total: <strong class="text-slate-800" x-text="liveCalculation.formattedTotal">Rp 0</strong></span>
                        </div>
                    </div>
                </div>

                <!-- Modal Footer -->
                <div class="sticky bottom-0 z-20 shrink-0 bg-slate-50 px-6 py-4 border-t border-slate-200 flex items-center justify-end gap-3">
                    <x-button variant="secondary" size="sm" @click="closeCreateModal()">
                        Batal
                    </x-button>
                    <x-button type="submit" variant="primary" size="sm">
                        Simpan Pengajuan
                    </x-button>
                </div>
            </form>
        </x-modal>

        <!-- ========================================== -->
        <!-- MODAL 2: DETAIL PENGAJUAN & KALKULASI TAGIHAN (MENGGUNAKAN x-modal & x-button) -->
        <!-- ========================================== -->
        <x-modal show="detailModalOpen" 
                 onClose="detailModalOpen = false" 
                 title="Detail Pengajuan & Rincian Tagihan" 
                 subtitle="Kalkulasi pembayaran cicilan transparan nasabah PT Capella Multidana" 
                 maxWidth="3xl">

            <div class="p-5 sm:p-6 space-y-5" x-show="!loadingDetail">
                <!-- Top Summary Cards -->
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 sm:gap-4">
                    <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200">
                        <span class="text-xs font-semibold uppercase text-slate-400">Nama Nasabah</span>
                        <div class="text-base font-bold text-slate-900 mt-1" x-text="detailData.customer_name"></div>
                        <div class="text-xs text-slate-500 mt-0.5">
                            Pendapatan: <span class="font-medium text-slate-700" x-text="detailData.formatted_monthly_income"></span>
                        </div>
                    </div>

                    <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200">
                        <span class="text-xs font-semibold uppercase text-slate-400">Tipe & Status</span>
                        <div class="mt-1 flex items-center gap-2">
                            <span class="px-2.5 py-0.5 rounded-full text-xs font-bold bg-slate-200 text-slate-800" x-text="detailData.loan_type_label"></span>
                            <span class="px-2.5 py-0.5 rounded-full text-xs font-bold" 
                                  :class="detailData.status_value === 'approved' ? 'bg-emerald-100 text-emerald-800' : (detailData.status_value === 'rejected' ? 'bg-rose-100 text-rose-800' : 'bg-amber-100 text-amber-800')"
                                  x-text="detailData.status_label"></span>
                        </div>
                        <div class="text-xs text-slate-500 mt-1.5" x-text="detailData.created_at"></div>
                    </div>

                    <div class="p-4 rounded-2xl bg-amber-50/70 border border-amber-200">
                        <span class="text-xs font-semibold uppercase text-amber-800">Tagihan per Bulan</span>
                        <div class="text-xl font-extrabold text-amber-950 mt-1" x-text="detailData.formatted_monthly_installment"></div>
                        <div class="text-xs text-amber-800 mt-0.5">
                            Tenor: <span class="font-bold" x-text="detailData.tenor_months + ' Bulan'"></span>
                        </div>
                    </div>
                </div>

                <!-- Rincian Komponen Tagihan -->
                <div class="rounded-2xl border border-slate-200 overflow-hidden">
                    <div class="bg-slate-50 px-4 py-3 border-b border-slate-200 font-bold text-xs uppercase tracking-wider text-slate-700 flex items-center justify-between">
                        <span>Rincian Komponen Pembayaran Tagihan</span>
                        <span class="text-slate-500 font-normal">Suku Bunga Flat 0.9%/bln</span>
                    </div>
                    <div class="p-4 grid grid-cols-1 sm:grid-cols-2 gap-4 text-xs sm:text-sm">
                        <div class="space-y-2">
                            <div class="flex justify-between py-1 border-b border-slate-100">
                                <span class="text-slate-500">Nominal Pokok Pembiayaan:</span>
                                <span class="font-bold text-slate-900" x-text="detailData.formatted_loan_amount"></span>
                            </div>
                            <div class="flex justify-between py-1 border-b border-slate-100">
                                <span class="text-slate-500">Tenor:</span>
                                <span class="font-semibold text-slate-900" x-text="detailData.tenor_months + ' Bulan'"></span>
                            </div>
                            <div class="flex justify-between py-1 border-b border-slate-100">
                                <span class="text-slate-500">Suku Bunga / Margin:</span>
                                <span class="font-semibold text-slate-900">0.9% / bulan (Flat)</span>
                            </div>
                            <div class="flex justify-between py-1">
                                <span class="text-slate-500">Rasio Angsuran / Gaji (DTI):</span>
                                <span class="font-bold" 
                                      :class="detailData.debt_to_income_ratio > 50 ? 'text-rose-600' : 'text-emerald-600'"
                                      x-text="detailData.debt_to_income_ratio + '%'"></span>
                            </div>
                        </div>

                        <div class="space-y-2 sm:border-l sm:border-slate-100 sm:pl-4">
                            <div class="flex justify-between py-1 border-b border-slate-100">
                                <span class="text-slate-500">Pokok Angsuran / Bulan:</span>
                                <span class="font-semibold text-slate-800" x-text="detailData.calculation ? detailData.calculation.formatted_principal_per_month : ''"></span>
                            </div>
                            <div class="flex justify-between py-1 border-b border-slate-100">
                                <span class="text-slate-500">Bunga Margin / Bulan:</span>
                                <span class="font-semibold text-slate-800" x-text="detailData.calculation ? detailData.calculation.formatted_interest_per_month : ''"></span>
                            </div>
                            <div class="flex justify-between py-1 border-b border-slate-100 bg-amber-50/50 px-2 rounded-lg">
                                <span class="font-bold text-amber-900">Total Tagihan / Bulan:</span>
                                <span class="font-extrabold text-amber-950" x-text="detailData.formatted_monthly_installment"></span>
                            </div>
                            <div class="flex justify-between py-1 text-xs">
                                <span class="text-slate-400">Total Pengembalian:</span>
                                <span class="font-semibold text-slate-700" x-text="detailData.calculation ? detailData.calculation.formatted_total_repayment : ''"></span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Catatan atau Alasan Penolakan -->
                <div x-show="detailData.notes || detailData.rejection_reason" class="p-4 rounded-xl bg-slate-50 border border-slate-200 text-xs space-y-2">
                    <div x-show="detailData.notes">
                        <span class="font-bold text-slate-700 uppercase tracking-wider block">Catatan Pengajuan:</span>
                        <p class="text-slate-600 mt-0.5" x-text="detailData.notes"></p>
                    </div>
                    <div x-show="detailData.rejection_reason" class="border-t border-slate-200/80 pt-2">
                        <span class="font-bold text-rose-700 uppercase tracking-wider block">Alasan Penolakan:</span>
                        <p class="text-rose-800 mt-0.5 font-medium" x-text="detailData.rejection_reason"></p>
                    </div>
                </div>

                <!-- Collapsible Jadwal Angsuran DENGAN ANIMASI HALUS -->
                <div x-data="{ showSchedule: false }" class="rounded-xl border border-slate-200 overflow-hidden">
                    <button @click="showSchedule = !showSchedule" 
                            type="button" 
                            class="w-full px-4 py-3 bg-slate-50 text-left font-bold text-xs uppercase tracking-wider text-slate-700 flex items-center justify-between hover:bg-slate-100 transition cursor-pointer">
                        <span>Simulasi Jadwal Pembayaran Angsuran (Tabel Amortisasi)</span>
                        <svg class="w-4 h-4 text-slate-400 transform transition-transform duration-300" :class="showSchedule ? 'rotate-180 text-amber-600' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>

                    <!-- Smooth Expand/Collapse Container -->
                    <div x-show="showSchedule" 
                         x-transition:enter="transition-all ease-out duration-300 transform"
                         x-transition:enter-start="opacity-0 -translate-y-2"
                         x-transition:enter-end="opacity-100 translate-y-0"
                         x-transition:leave="transition-all ease-in duration-200 transform"
                         x-transition:leave-start="opacity-100 translate-y-0"
                         x-transition:leave-end="opacity-0 -translate-y-2"
                         class="border-t border-slate-200">
                        <div class="max-h-56 overflow-y-auto">
                            <table class="w-full text-xs text-left text-slate-600">
                                <thead class="bg-slate-100 text-[11px] font-bold uppercase text-slate-600 sticky top-0">
                                    <tr>
                                        <th class="py-2.5 px-3">Bulan Ke</th>
                                        <th class="py-2.5 px-3 text-right">Pokok</th>
                                        <th class="py-2.5 px-3 text-right">Bunga</th>
                                        <th class="py-2.5 px-3 text-right">Total Angsuran</th>
                                        <th class="py-2.5 px-3 text-right">Sisa Pokok</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-100">
                                    <template x-for="item in detailData.schedule" :key="item.month">
                                        <tr class="hover:bg-slate-50 transition-colors">
                                            <td class="py-2 px-3 font-semibold text-slate-800" x-text="'Bulan ' + item.month"></td>
                                            <td class="py-2 px-3 text-right" x-text="item.formatted_principal"></td>
                                            <td class="py-2 px-3 text-right" x-text="item.formatted_interest"></td>
                                            <td class="py-2 px-3 text-right font-bold text-amber-800" x-text="item.formatted_installment"></td>
                                            <td class="py-2 px-3 text-right text-slate-500" x-text="item.formatted_remaining_balance"></td>
                                        </tr>
                                    </template>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

            </div>

            <!-- Modal Detail Loading Indicator -->
            <div x-show="loadingDetail" class="py-16 text-center text-slate-400">
                <svg class="w-8 h-8 mx-auto animate-spin text-amber-500" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                <span class="mt-2 block text-xs font-semibold text-slate-500">Memuat rincian kalkulasi...</span>
            </div>

            <!-- Sticky Modal Footer -->
            <x-slot name="footer">
                <x-button variant="secondary" size="sm" @click="detailModalOpen = false">
                    Tutup
                </x-button>

                @if ($isAnalyst)
                    <div class="flex items-center gap-2" x-show="detailData.status_value === 'pending'">
                        <x-button variant="rose-soft" size="sm" @click="detailModalOpen = false; confirmReject(detailData.loan ? detailData.loan.id : 0, detailData.customer_name)">
                            Tolak Pengajuan
                        </x-button>
                        <x-button variant="emerald" size="sm" @click="detailModalOpen = false; confirmApprove(detailData.loan ? detailData.loan.id : 0, detailData.customer_name, detailData.formatted_loan_amount)">
                            Setujui Pengajuan
                        </x-button>
                    </div>
                @endif
            </x-slot>
        </x-modal>

        <!-- ========================================== -->
        <!-- MODAL 3: ERROR / WARNING POPUP SYARAT (MENGGUNAKAN x-modal & x-button) -->
        <!-- ========================================== -->
        <x-modal show="errorModalOpen" 
                 onClose="errorModalOpen = false" 
                 maxWidth="md" 
                 headerVariant="white">
            <div class="p-6">
                <div class="flex items-start gap-4">
                    <div class="w-11 h-11 rounded-2xl bg-rose-100 text-rose-600 flex items-center justify-center shrink-0">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-base font-bold text-slate-900 leading-tight" x-text="errorModalTitle">Pengajuan Belum Memenuhi Syarat</h3>
                        <p class="text-xs text-rose-700 mt-1.5 font-medium leading-relaxed" x-text="errorModalMessage"></p>
                    </div>
                </div>

                <div class="mt-6 flex justify-end">
                    <x-button variant="dark" size="sm" @click="errorModalOpen = false">
                        Mengerti & Perbaiki
                    </x-button>
                </div>
            </div>
        </x-modal>

        <!-- ========================================== -->
        <!-- MODAL 4: DIALOG KONFIRMASI PERSETUJUAN (MENGGUNAKAN x-modal & x-button) -->
        <!-- ========================================== -->
        <x-modal show="approveModalOpen" 
                 onClose="approveModalOpen = false" 
                 maxWidth="md" 
                 headerVariant="white">
            <div class="p-6">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 rounded-2xl bg-emerald-100 text-emerald-600 flex items-center justify-center shrink-0">
                        <svg class="w-6 h-6 text-emerald-600" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-base font-bold text-slate-900 leading-tight">Konfirmasi Persetujuan</h3>
                        <p class="text-xs text-slate-500 mt-0.5">Persetujuan berkas pengajuan kredit nasabah</p>
                    </div>
                </div>

                <div class="my-5 p-4 rounded-2xl bg-emerald-50/70 border border-emerald-200/80 text-xs text-slate-700 space-y-1.5">
                    <p>Anda akan menyetujui pengajuan kredit atas nama:</p>
                    <p class="text-sm font-bold text-slate-900" x-text="targetLoan.customer_name"></p>
                    <p class="text-xs text-emerald-800 font-semibold">Nominal: <span x-text="targetLoan.formatted_amount"></span></p>
                </div>

                <form :action="'/loans/' + targetLoan.id + '/approve'" method="POST" class="flex items-center justify-end gap-2.5">
                    @csrf
                    <x-button variant="secondary" size="sm" @click="approveModalOpen = false">
                        Batal
                    </x-button>
                    <x-button type="submit" variant="emerald" size="sm">
                        Ya, Setujui Pengajuan
                    </x-button>
                </form>
            </div>
        </x-modal>

        <!-- ========================================== -->
        <!-- MODAL 5: DIALOG KONFIRMASI PENOLAKAN (MENGGUNAKAN x-modal & x-button) -->
        <!-- ========================================== -->
        <x-modal show="rejectModalOpen" 
                 onClose="rejectModalOpen = false" 
                 maxWidth="md" 
                 headerVariant="white">
            <div class="p-6">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 rounded-2xl bg-rose-100 text-rose-600 flex items-center justify-center shrink-0">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-base font-bold text-slate-900 leading-tight">Konfirmasi Penolakan</h3>
                        <p class="text-xs text-slate-500 mt-0.5">Penolakan berkas pengajuan kredit nasabah</p>
                    </div>
                </div>

                <div class="my-4 text-xs text-slate-600">
                    Apakah Anda yakin ingin menolak pengajuan kredit nasabah:
                    <strong class="block text-sm text-slate-900 mt-1" x-text="targetLoan.customer_name"></strong>
                </div>

                <form :action="'/loans/' + targetLoan.id + '/reject'" method="POST" class="space-y-4">
                    @csrf
                    <div>
                        <label class="block text-xs font-bold uppercase text-slate-700 mb-1">Alasan Penolakan</label>
                        <textarea name="rejection_reason" 
                                  rows="2" 
                                  placeholder="Contoh: Beban tagihan melebihi batas kemampuan bayar..."
                                  class="w-full px-3.5 py-2 rounded-xl border border-slate-200 text-xs focus:outline-none focus:ring-2 focus:ring-rose-500/30 focus:border-rose-500 transition"></textarea>
                    </div>

                    <div class="flex items-center justify-end gap-2.5">
                        <x-button variant="secondary" size="sm" @click="rejectModalOpen = false">
                            Batal
                        </x-button>
                        <x-button type="submit" variant="rose" size="sm">
                            Ya, Tolak Pengajuan
                        </x-button>
                    </div>
                </form>
            </div>
        </x-modal>

    </div>

    <!-- Alpine.js Application Logic with Client-Side Instant Filtering -->
    <script>
        function loanPortalApp() {
            return {
                createModalOpen: false,
                detailModalOpen: false,
                approveModalOpen: false,
                rejectModalOpen: false,
                errorModalOpen: false,
                errorModalTitle: '',
                errorModalMessage: '',
                loadingDetail: false,

                // Custom Dropdown States
                statusDropdownOpen: false,
                typeDropdownOpen: false,
                tenorDropdownOpen: false,
                selectedStatusLabel: 'Semua Status',
                selectedTypeLabel: 'Semua Tipe',
                selectedTenorLabel: '12 Bulan (1 Tahun)',

                // Filter States for 0ms Instant Client-Side Filter
                searchQuery: '',
                statusFilter: 'all',
                typeFilter: 'all',

                // Master List of Loans Loaded from Server
                allLoans: @json($loans->items()).map(item => ({
                    id: item.id,
                    customer_name: item.customer_name,
                    loan_type: item.loan_type,
                    type_label: item.loan_type === 'motor' ? 'Sepeda Motor' : (item.loan_type === 'mobil' ? 'Mobil' : 'Multiguna'),
                    loan_amount: parseFloat(item.loan_amount),
                    formatted_amount: 'Rp ' + new Intl.NumberFormat('id-ID').format(item.loan_amount),
                    tenor_months: item.tenor_months,
                    monthly_income: parseFloat(item.monthly_income),
                    formatted_income: 'Rp ' + new Intl.NumberFormat('id-ID').format(item.monthly_income),
                    monthly_installment: parseFloat(item.monthly_installment),
                    formatted_installment: 'Rp ' + new Intl.NumberFormat('id-ID').format(item.monthly_installment),
                    status: item.status,
                    status_label: item.status === 'approved' ? 'Disetujui' : (item.status === 'rejected' ? 'Ditolak' : 'Menunggu'),
                    formatted_date: new Date(item.created_at).toLocaleDateString('id-ID', {
                        day: '2-digit',
                        month: 'short',
                        year: 'numeric'
                    }),
                })),

                // Row Filter Match Evaluator (for smooth x-show / x-transition on table rows)
                matchesFilter(loan) {
                    const matchSearch = this.searchQuery === '' || 
                        loan.customer_name.toLowerCase().includes(this.searchQuery.toLowerCase());
                    const matchStatus = this.statusFilter === 'all' || 
                        loan.status === this.statusFilter;
                    const matchType = this.typeFilter === 'all' || 
                        loan.loan_type === this.typeFilter;

                    return matchSearch && matchStatus && matchType;
                },

                get visibleCount() {
                    return this.allLoans.filter(loan => this.matchesFilter(loan)).length;
                },

                setStatusFilter(val, label) {
                    this.statusFilter = val;
                    this.selectedStatusLabel = label;
                    this.statusDropdownOpen = false;
                },

                setTypeFilter(val, label) {
                    this.typeFilter = val;
                    this.selectedTypeLabel = label;
                    this.typeDropdownOpen = false;
                },

                setTenor(months, label) {
                    this.formData.tenor_months = months;
                    this.selectedTenorLabel = label;
                    this.tenorDropdownOpen = false;
                    this.calculateLive();
                },

                resetFilters() {
                    this.searchQuery = '';
                    this.statusFilter = 'all';
                    this.selectedStatusLabel = 'Semua Status';
                    this.typeFilter = 'all';
                    this.selectedTypeLabel = 'Semua Tipe';
                    this.statusDropdownOpen = false;
                    this.typeDropdownOpen = false;
                },

                // Form Data
                formData: {
                    customer_name: '',
                    loan_type: 'motor',
                    tenor_months: 12,
                    notes: '',
                },
                rawAmount: 0,
                formattedAmount: '',
                rawIncome: 0,
                formattedIncome: '',

                // Live Preview Calculation
                liveCalculation: {
                    formattedPrincipal: 'Rp 0',
                    formattedInterest: 'Rp 0',
                    formattedMonthly: 'Rp 0',
                    formattedTotal: 'Rp 0',
                },

                // Detail Data
                detailData: {},

                // Target Loan for Action Dialogs
                targetLoan: {
                    id: null,
                    customer_name: '',
                    formatted_amount: '',
                },

                openCreateModal() {
                    this.createModalOpen = true;
                    this.calculateLive();
                },

                closeCreateModal() {
                    this.createModalOpen = false;
                },

                updateAmount(val) {
                    const clean = val.replace(/[^\d]/g, '');
                    this.rawAmount = clean ? parseInt(clean, 10) : 0;
                    this.formattedAmount = clean ? new Intl.NumberFormat('id-ID').format(this.rawAmount) : '';
                    this.calculateLive();
                },

                updateIncome(val) {
                    const clean = val.replace(/[^\d]/g, '');
                    this.rawIncome = clean ? parseInt(clean, 10) : 0;
                    this.formattedIncome = clean ? new Intl.NumberFormat('id-ID').format(this.rawIncome) : '';
                },

                calculateLive() {
                    if (this.rawAmount <= 0 || this.formData.tenor_months <= 0) {
                        this.liveCalculation = {
                            formattedPrincipal: 'Rp 0',
                            formattedInterest: 'Rp 0',
                            formattedMonthly: 'Rp 0',
                            formattedTotal: 'Rp 0',
                        };
                        return;
                    }

                    const principal = Math.round(this.rawAmount / this.formData.tenor_months);
                    const interest = Math.round(this.rawAmount * 0.009); // 0.9% flat/month
                    const monthly = principal + interest;
                    const total = monthly * this.formData.tenor_months;

                    const fmt = (num) => 'Rp ' + new Intl.NumberFormat('id-ID').format(num);

                    this.liveCalculation = {
                        formattedPrincipal: fmt(principal),
                        formattedInterest: fmt(interest),
                        formattedMonthly: fmt(monthly),
                        formattedTotal: fmt(total),
                    };
                },

                // Validate and show interactive error popup if Behaviour rules are violated
                validateAndSubmit() {
                    // Behaviour 1: Pendapatan bulanan < 1 juta
                    if (this.rawIncome < 1000000) {
                        this.triggerErrorPopup(
                            'Pendapatan Bulanan Tidak Memenuhi Syarat',
                            'Nasabah belum dapat mengajukan pinjaman. Sesuai kebijakan kredit PT Capella Multidana, batas minimal pendapatan bulanan nasabah adalah Rp 1.000.000.'
                        );
                        return;
                    }

                    // Behaviour 2: Maksimal pinjaman 200 juta
                    if (this.rawAmount > 200000000) {
                        this.triggerErrorPopup(
                            'Nominal Pinjaman Melebihi Batas',
                            'Nominal maksimal pinjaman yang dapat disetujui adalah Rp 200.000.000. Harap sesuaikan kembali nominal pengajuan.'
                        );
                        return;
                    }

                    // Behaviour 3: Maksimal tenor 24 bulan
                    if (this.formData.tenor_months > 24) {
                        this.triggerErrorPopup(
                            'Tenor Melebihi Batas',
                            'Tenor pinjaman tertinggi yang diizinkan adalah 24 bulan.'
                        );
                        return;
                    }

                    // Behaviour 4: Cek pengajuan sebelumnya
                    const currentName = this.formData.customer_name.trim().toLowerCase();
                    const existingCount = this.allLoans.filter(l => l.customer_name.trim().toLowerCase() === currentName).length;
                    if (existingCount >= 3) {
                        this.triggerErrorPopup(
                            'Batas Maksimal Pengajuan Tercapai',
                            'Nasabah atas nama "' + this.formData.customer_name + '" telah mencapai batas maksimal 3 kali pengajuan pembiayaan.'
                        );
                        return;
                    }

                    // Jika valid, submit form
                    document.getElementById('createLoanForm').submit();
                },

                triggerErrorPopup(title, message) {
                    this.errorModalTitle = title;
                    this.errorModalMessage = message;
                    this.errorModalOpen = true;
                },

                openDetailModal(loanId) {
                    this.loadingDetail = true;
                    this.detailModalOpen = true;
                    this.detailData = {};

                    fetch('/loans/' + loanId, {
                        headers: {
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    })
                    .then(res => res.json())
                    .then(data => {
                        this.detailData = data;
                        this.loadingDetail = false;
                    })
                    .catch(err => {
                        console.error('Error fetching loan details:', err);
                        this.loadingDetail = false;
                    });
                },

                confirmApprove(id, name, amount) {
                    this.targetLoan = {
                        id: id,
                        customer_name: name,
                        formatted_amount: amount,
                    };
                    this.approveModalOpen = true;
                },

                confirmReject(id, name) {
                    this.targetLoan = {
                        id: id,
                        customer_name: name,
                        formatted_amount: '',
                    };
                    this.rejectModalOpen = true;
                }
            };
        }
    </script>
</x-app-layout>