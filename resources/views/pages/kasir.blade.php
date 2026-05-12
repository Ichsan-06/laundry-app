@extends('layouts.app')

@section('title', 'Kasir - Laundry Track')

@section('content')
<div class="mx-auto max-w-[1600px] pb-10" x-data="{
    service: 'self_service',
    customerType: 'member',
    searchMember: '',
    selectedMember: null,
    machineType: 'ALL',
    selectedDuration: 'Normal',
    selectedMachines: [],
    paymentMethod: 'CASH',
    amountReceived: 0,
    discountPercent: 0,
    taxPercent: 0,
    showNewMemberModal: false,
    newMember: { nama: '', no_hp: '', email: '' },

    // Data from Backend
    allMembers: {{ json_encode($members) }},
    allMachines: {{ json_encode($machines) }},
    allServices: {{ json_encode($services) }},
    allAddons: {{ json_encode($addons) }},

    // Drop Off State
    dropOff: {
        details: [{ package_id: '', weight: 1, note: '' }],
        selectedAddons: [],
        items: [{ nama: '', qty: 1, note: '' }],
        catatan: '',
        estimasiSelesai: new Date(new Date().getTime() + 24 * 60 * 60 * 1000).toISOString().split('T')[0] + ' 10:00',
    },

    addServiceDetail() {
        this.dropOff.details.push({ package_id: '', weight: 1, note: '' });
    },

    removeServiceDetail(index) {
        if (this.dropOff.details.length > 1) {
            this.dropOff.details.splice(index, 1);
        }
    },

    get filteredMembers() {
        if (this.searchMember === '') return [];
        return this.allMembers.filter(m => 
            m.nama.toLowerCase().includes(this.searchMember.toLowerCase()) || 
            m.id_member.toLowerCase().includes(this.searchMember.toLowerCase()) ||
            m.no_hp.includes(this.searchMember)
        ).slice(0, 5);
    },

    get filteredMachines() {
        if (this.machineType === 'ALL') return this.allMachines;
        return this.allMachines.filter(m => m.machine_type === this.machineType);
    },

    toggleMachine(machine) {
        if (machine.status !== 'AVAILABLE') return;
        
        const index = this.selectedMachines.findIndex(m => m.id === machine.id);
        if (index > -1) {
            this.selectedMachines.splice(index, 1);
        } else {
            if (this.machineType === 'ALL') {
                if (this.selectedMachines.length < 2) {
                    // Check if already has same type
                    const hasSameType = this.selectedMachines.some(m => m.machine_type === machine.machine_type);
                    if (!hasSameType) {
                        this.selectedMachines.push(machine);
                    } else {
                        alert('You already selected a ' + machine.machine_type);
                    }
                } else {
                    alert('You can only select up to 2 machines (1 Washer & 1 Dryer) in ALL mode');
                }
            } else {
                this.selectedMachines = [machine];
            }
        }
    },

    // removed toggleServicePackage since we use dropdowns now

    toggleAddon(addon) {
        const index = this.dropOff.selectedAddons.findIndex(a => a.id === addon.id);
        if (index > -1) {
            this.dropOff.selectedAddons.splice(index, 1);
        } else {
            this.dropOff.selectedAddons.push(addon);
        }
    },

    addItem() {
        this.dropOff.items.push({ nama: '', qty: 1, note: '' });
    },

    removeItem(index) {
        this.dropOff.items.splice(index, 1);
    },

    get subtotal() {
        if (this.service === 'self_service') {
            return this.selectedMachines.reduce((sum, machine) => {
                const duration = machine.durations.find(d => d.duration_type === (machine.machine_type === 'WASHER' ? 'WASH' : 'DRY')) || machine.durations[0];
                return sum + parseFloat(duration?.price || 0);
            }, 0);
        } else {
            let total = 0;
            // Services: weight * price_per_kg
            this.dropOff.details.forEach(d => {
                const pkg = this.allServices.find(s => s.id === d.package_id);
                if (pkg) {
                    const weight = Math.max(parseFloat(d.weight) || 0, parseFloat(pkg.berat_minimal) || 0);
                    total += weight * parseFloat(pkg.harga_per_kg);
                }
            });
            // Addons: fixed price
            this.dropOff.selectedAddons.forEach(a => {
                total += parseFloat(a.harga);
            });
            return total;
        }
    },

    get discountAmount() {
        return (this.subtotal * (parseFloat(this.discountPercent) || 0)) / 100;
    },

    get taxAmount() {
        return ((this.subtotal - this.discountAmount) * (parseFloat(this.taxPercent) || 0)) / 100;
    },

    get totalAmount() {
        return this.subtotal - this.discountAmount + this.taxAmount;
    },

    get changeAmount() {
        if (this.paymentMethod !== 'CASH') return 0;
        const change = (parseFloat(this.amountReceived) || 0) - this.totalAmount;
        return Math.max(0, change);
    },

    async saveMember() {
        try {
            const response = await fetch('{{ route('kasir.member.store') }}', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                body: JSON.stringify(this.newMember)
            });
            const data = await response.json();
            if (data.success) {
                this.allMembers.push(data.member);
                this.selectedMember = data.member;
                this.showNewMemberModal = false;
                this.searchMember = '';
            }
        } catch (error) { alert('Failed to save member'); }
    }
}">
    {{-- Header --}}
    <div class="mb-6 flex items-center justify-between">
        <div>
            <h2 class="text-xl font-extrabold text-slate-900">Kasir</h2>
            <p class="text-sm font-medium text-slate-400">Buat Transaksi Baru</p>
        </div>
        <div class="flex items-center gap-4">
            <button class="rounded-xl border border-slate-200 bg-white px-4 py-2 text-sm font-bold text-slate-600 transition hover:bg-slate-50">
                <div class="flex items-center gap-2">
                    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <circle cx="12" cy="12" r="10"></circle>
                        <polyline points="12 6 12 12 16 14"></polyline>
                    </svg>
                    Riwayat Transaksi
                </div>
            </button>
            <div class="flex items-center gap-3">
                <div class="text-right">
                    <p class="text-sm font-extrabold text-slate-900">Kasir 1</p>
                    <p class="text-[11px] font-bold text-slate-400">Kasir</p>
                </div>
                <div class="h-10 w-10 overflow-hidden rounded-full bg-slate-100 ring-2 ring-slate-50">
                    <img src="https://ui-avatars.com/api/?name=Kasir+1&background=6d55e8&color=fff" alt="">
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 gap-6 lg:grid-cols-12">
        
        {{-- Column 1: Config (3/12) --}}
        <div class="lg:col-span-3 space-y-6">
            {{-- 1. Pilih Jenis Layanan --}}
            <div class="rounded-3xl bg-white p-6 shadow-sm ring-1 ring-slate-100">
                <h3 class="mb-4 text-sm font-extrabold text-slate-900">1. Pilih Jenis Layanan</h3>
                <div class="grid grid-cols-2 gap-3">
                    <button @click="service = 'self_service'" :class="service === 'self_service' ? 'ring-2 ring-primary-600 bg-primary-50/50' : 'bg-slate-50'" class="flex flex-col items-center justify-center rounded-2xl p-4 transition">
                        <svg class="mb-2 h-6 w-6 text-primary-600" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <rect x="6" y="3" width="12" height="18" rx="2"></rect>
                            <circle cx="12" cy="14" r="3"></circle>
                        </svg>
                        <span class="text-xs font-extrabold text-slate-900">Self Service</span>
                        <span class="mt-1 text-[9px] font-medium text-slate-400">Pelanggan cuci sendiri</span>
                    </button>
                    <button @click="service = 'drop_off'" :class="service === 'drop_off' ? 'ring-2 ring-primary-600 bg-primary-50/50' : 'bg-slate-50'" class="flex flex-col items-center justify-center rounded-2xl p-4 transition">
                        <svg class="mb-2 h-6 w-6 text-primary-600" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M16 21v-2a4 4 0 0 0-8 0v2"></path>
                            <circle cx="12" cy="7" r="4"></circle>
                        </svg>
                        <span class="text-xs font-extrabold text-slate-900">Drop Off</span>
                        <span class="mt-1 text-[9px] font-medium text-slate-400">Karyawan yang mencuci</span>
                    </button>
                </div>
            </div>

            {{-- 2. Data Pelanggan --}}
            <div class="rounded-3xl bg-white p-6 shadow-sm ring-1 ring-slate-100">
                <h3 class="mb-4 text-sm font-extrabold text-slate-900">2. Data Pelanggan</h3>
                <div class="mb-4 flex items-center gap-4">
                    <label class="flex items-center gap-2">
                        <input type="radio" name="cust_type" value="member" x-model="customerType" class="text-primary-600 focus:ring-primary-500">
                        <span class="text-xs font-bold text-slate-600">Member</span>
                    </label>
                    <label class="flex items-center gap-2">
                        <input type="radio" name="cust_type" value="non_member" x-model="customerType" class="text-primary-600 focus:ring-primary-500">
                        <span class="text-xs font-bold text-slate-600">Non Member</span>
                    </label>
                </div>
                
                <div x-show="customerType === 'member'" class="space-y-4">
                    <p class="text-[10px] font-extrabold uppercase tracking-widest text-slate-400">Cari Member</p>
                    <div class="relative">
                        <input type="text" x-model="searchMember" placeholder="Masukkan nama / no hp / ID Member" class="block w-full rounded-xl border-slate-100 bg-slate-50 py-2.5 pl-4 pr-10 text-xs font-bold text-slate-900 focus:ring-2 focus:ring-primary-500/20">
                        <div class="absolute inset-y-0 right-0 flex items-center pr-3">
                            <svg class="h-4 w-4 text-slate-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"></circle><path d="m21 21-4.3-4.3"></path></svg>
                        </div>
                        
                        {{-- Member Search Results --}}
                        <div x-show="filteredMembers.length > 0" class="absolute left-0 right-0 top-full z-30 mt-1 rounded-xl bg-white p-2 shadow-xl ring-1 ring-slate-100">
                            <template x-for="m in filteredMembers" :key="m.id">
                                <button @click="selectedMember = m; searchMember = ''" class="flex w-full items-center gap-3 rounded-lg px-3 py-2 text-left hover:bg-slate-50">
                                    <div class="h-8 w-8 overflow-hidden rounded-full bg-slate-100"><img :src="'https://ui-avatars.com/api/?name=' + encodeURIComponent(m.nama) + '&background=f1f5f9&color=64748b'" alt=""></div>
                                    <div class="min-w-0">
                                        <p class="truncate text-xs font-extrabold text-slate-900" x-text="m.nama"></p>
                                        <p class="text-[10px] font-bold text-slate-400" x-text="m.id_member"></p>
                                    </div>
                                </button>
                            </template>
                        </div>
                    </div>
                    
                    <template x-if="selectedMember">
                        <div class="flex items-center justify-between rounded-2xl bg-indigo-50/50 p-4 ring-1 ring-inset ring-indigo-100">
                            <div class="flex items-center gap-3">
                                <div class="flex h-10 w-10 items-center justify-center rounded-full bg-indigo-600 text-xs font-extrabold text-white" x-text="selectedMember.nama.charAt(0).toUpperCase()"></div>
                                <div>
                                    <h4 class="text-xs font-extrabold text-slate-900" x-text="selectedMember.nama"></h4>
                                    <p class="text-[10px] font-bold text-slate-400" x-text="'ID Member: ' + selectedMember.id_member"></p>
                                </div>
                            </div>
                            <div class="text-right">
                                <p class="text-[10px] font-bold text-slate-400 uppercase">Saldo</p>
                                <p class="text-xs font-extrabold text-emerald-600" x-text="'Rp ' + parseFloat(selectedMember.saldo).toLocaleString('id-ID')"></p>
                            </div>
                        </div>
                    </template>
                </div>
                
                <button @click="showNewMemberModal = true" class="mt-4 flex h-10 w-full items-center justify-center rounded-xl border border-dashed border-slate-200 bg-slate-50 text-slate-400 transition hover:bg-white hover:text-primary-600 hover:ring-1 hover:ring-primary-500">
                    <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 5v14M5 12h14"></path></svg>
                </button>
            </div>

            {{-- 3. Pilih Mesin (Filter) --}}
            <div x-show="service === 'self_service'" class="rounded-3xl bg-white p-6 shadow-sm ring-1 ring-slate-100">
                <h3 class="mb-4 text-sm font-extrabold text-slate-900">3. Pilih Mesin</h3>
                <p class="mb-3 text-[11px] font-bold text-slate-400">Pilih mesin yang akan digunakan</p>
                
                <div class="space-y-4">
                    <div>
                        <p class="mb-2 text-[10px] font-extrabold uppercase tracking-widest text-slate-400">Tipe Mesin</p>
                        <div class="flex gap-2 rounded-xl bg-slate-50 p-1">
                            <button @click="machineType = 'ALL'; selectedMachines = []" :class="machineType === 'ALL' ? 'bg-white text-primary-600 shadow-sm' : 'text-slate-400'" class="flex-1 rounded-lg py-1.5 text-[11px] font-extrabold transition">Semua</button>
                            <button @click="machineType = 'WASHER'; selectedMachines = []" :class="machineType === 'WASHER' ? 'bg-white text-primary-600 shadow-sm' : 'text-slate-400'" class="flex-1 rounded-lg py-1.5 text-[11px] font-extrabold transition">Washer</button>
                            <button @click="machineType = 'DRYER'; selectedMachines = []" :class="machineType === 'DRYER' ? 'bg-white text-primary-600 shadow-sm' : 'text-slate-400'" class="flex-1 rounded-lg py-1.5 text-[11px] font-extrabold transition">Dryer</button>
                        </div>
                    </div>
                    
                    <div>
                        <p class="mb-2 text-[10px] font-extrabold uppercase tracking-widest text-slate-400">Durasi</p>
                        <div class="flex gap-2">
                            <button @click="selectedDuration = 'Normal'" :class="selectedDuration === 'Normal' ? 'bg-indigo-50 text-indigo-600 ring-1 ring-indigo-200' : 'bg-slate-50 text-slate-500'" class="flex-1 rounded-xl py-2 text-[11px] font-bold transition">Normal</button>
                            <button @click="selectedDuration = 'Cepat'" :class="selectedDuration === 'Cepat' ? 'bg-indigo-50 text-indigo-600 ring-1 ring-indigo-200' : 'bg-slate-50 text-slate-500'" class="flex-1 rounded-xl py-2 text-[11px] font-bold transition">Cepat</button>
                            <button @click="selectedDuration = 'Ekstra'" :class="selectedDuration === 'Ekstra' ? 'bg-indigo-50 text-indigo-600 ring-1 ring-indigo-200' : 'bg-slate-50 text-slate-500'" class="flex-1 rounded-xl py-2 text-[11px] font-bold transition">Ekstra Cepat</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Column 2: Content (6/12) --}}
        <div class="lg:col-span-6 space-y-6">
            {{-- Drop Off Mode Content --}}
            <template x-if="service === 'drop_off'">
                <div class="space-y-6">
                    {{-- 4. Detail Cucian (Multiple) --}}
                    <div class="rounded-3xl bg-white p-8 shadow-sm ring-1 ring-slate-100">
                        <div class="mb-6 flex items-center justify-between">
                            <h3 class="text-sm font-extrabold text-slate-900">4. Detail Cucian</h3>
                            <button @click="addServiceDetail()" class="flex items-center gap-2 rounded-xl bg-primary-50 px-4 py-2 text-[10px] font-extrabold text-primary-600 transition hover:bg-primary-100">
                                <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M12 5v14M5 12h14"></path></svg>
                                Tambah Paket
                            </button>
                        </div>
                        
                        <div class="space-y-4">
                            <template x-for="(detail, index) in dropOff.details" :key="index">
                                <div class="relative space-y-4 rounded-2xl bg-slate-50/50 p-6 ring-1 ring-slate-100 animate-in fade-in slide-in-from-top-2">
                                    <div class="grid grid-cols-1 gap-6 sm:grid-cols-12">
                                        {{-- Service Package Dropdown --}}
                                        <div class="sm:col-span-5 space-y-2">
                                            <label class="text-[10px] font-extrabold uppercase tracking-widest text-slate-400">Pilih Paket</label>
                                            <select x-model="detail.package_id" class="block w-full rounded-xl border-slate-100 bg-white py-2.5 px-4 text-xs font-bold text-slate-900 focus:ring-2 focus:ring-primary-500/20">
                                                <option value="">-- Pilih Paket --</option>
                                                <template x-for="pkg in allServices" :key="pkg.id">
                                                    <option :value="pkg.id" x-text="pkg.nama_paket + ' (Rp ' + parseFloat(pkg.harga_per_kg).toLocaleString('id-ID') + '/kg)'"></option>
                                                </template>
                                            </select>
                                        </div>

                                        {{-- Weight Input --}}
                                        <div class="sm:col-span-3 space-y-2">
                                            <label class="text-[10px] font-extrabold uppercase tracking-widest text-slate-400">Berat (Kg)</label>
                                            <div class="relative">
                                                <input type="number" x-model="detail.weight" class="block w-full rounded-xl border-slate-100 bg-white py-2.5 px-4 text-xs font-bold text-slate-900 focus:ring-2 focus:ring-primary-500/20">
                                                <div class="absolute inset-y-0 right-0 flex items-center pr-4">
                                                    <span class="text-[10px] font-bold text-slate-400 uppercase">Kg</span>
                                                </div>
                                            </div>
                                        </div>

                                        {{-- Notes Input --}}
                                        <div class="sm:col-span-4 space-y-2">
                                            <label class="text-[10px] font-extrabold uppercase tracking-widest text-slate-400">Catatan Paket</label>
                                            <div class="flex gap-2">
                                                <input type="text" x-model="detail.note" placeholder="Contoh: Noda kerah" class="block w-full flex-1 rounded-xl border-slate-100 bg-white py-2.5 px-4 text-xs font-bold text-slate-900 focus:ring-2 focus:ring-primary-500/20">
                                                <button x-show="dropOff.details.length > 1" @click="removeServiceDetail(index)" class="rounded-xl bg-rose-50 p-2.5 text-rose-500 hover:bg-rose-100 transition">
                                                    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path></svg>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </template>
                            
                            {{-- Global Estimation for Transaction --}}
                            <div class="pt-4 border-t border-slate-100">
                                <div class="grid grid-cols-2 gap-6">
                                    <div class="space-y-2">
                                        <label class="text-[10px] font-extrabold uppercase tracking-widest text-slate-400">Catatan Global (Opsional)</label>
                                        <textarea x-model="dropOff.catatan" placeholder="Catatan untuk seluruh cucian..." rows="1" class="block w-full rounded-xl border-slate-100 bg-slate-50 py-2.5 px-4 text-xs font-bold text-slate-900 focus:ring-2 focus:ring-primary-500/20"></textarea>
                                    </div>
                                    <div class="space-y-2">
                                        <label class="text-[10px] font-extrabold uppercase tracking-widest text-slate-400">Estimasi Selesai</label>
                                        <input type="text" x-model="dropOff.estimasiSelesai" class="block w-full rounded-xl border-slate-100 bg-slate-50 py-2.5 px-4 text-xs font-bold text-slate-900 focus:ring-2 focus:ring-primary-500/20">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Item Detail (Multiple) --}}
                    <div class="rounded-3xl bg-white p-8 shadow-sm ring-1 ring-slate-100">
                        <div class="mb-6 flex items-center justify-between">
                            <h3 class="text-sm font-extrabold text-slate-900">5. Detail Item</h3>
                            <button @click="addItem()" class="flex items-center gap-2 rounded-xl bg-primary-50 px-4 py-2 text-[10px] font-extrabold text-primary-600 transition hover:bg-primary-100">
                                <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M12 5v14M5 12h14"></path></svg>
                                Tambah Item
                            </button>
                        </div>

                        <div class="space-y-3">
                            <template x-for="(item, index) in dropOff.items" :key="index">
                                <div class="flex items-end gap-3 animate-in fade-in slide-in-from-top-2">
                                    <div class="flex-1 space-y-1.5">
                                        <label x-show="index === 0" class="text-[9px] font-extrabold uppercase tracking-widest text-slate-400 ml-1">Nama Item</label>
                                        <input type="text" x-model="item.nama" placeholder="e.g. Kemeja, Selimut" class="block w-full rounded-xl border-slate-100 bg-slate-50 py-2.5 px-4 text-xs font-bold text-slate-900">
                                    </div>
                                    <div class="w-20 space-y-1.5">
                                        <label x-show="index === 0" class="text-[9px] font-extrabold uppercase tracking-widest text-slate-400 ml-1">Qty</label>
                                        <input type="number" x-model="item.qty" class="block w-full rounded-xl border-slate-100 bg-slate-50 py-2.5 px-4 text-xs font-bold text-slate-900">
                                    </div>
                                    <div class="flex-[1.5] space-y-1.5">
                                        <label x-show="index === 0" class="text-[9px] font-extrabold uppercase tracking-widest text-slate-400 ml-1">Catatan</label>
                                        <input type="text" x-model="item.note" placeholder="e.g. Warna Putih" class="block w-full rounded-xl border-slate-100 bg-slate-50 py-2.5 px-4 text-xs font-bold text-slate-900">
                                    </div>
                                    <button @click="removeItem(index)" class="mb-0.5 rounded-xl bg-rose-50 p-2.5 text-rose-500 hover:bg-rose-100 transition">
                                        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path></svg>
                                    </button>
                                </div>
                            </template>
                        </div>
                    </div>

                    {{-- 6. Opsi Tambahan (Add Ons) --}}
                    <div class="rounded-3xl bg-white p-8 shadow-sm ring-1 ring-slate-100">
                        <h3 class="mb-6 text-sm font-extrabold text-slate-900">6. Opsi Tambahan (Add Ons)</h3>
                        <div class="grid grid-cols-2 gap-4 sm:grid-cols-4">
                            <template x-for="addon in allAddons" :key="addon.id">
                                <button @click="toggleAddon(addon)" :class="dropOff.selectedAddons.find(a => a.id === addon.id) ? 'ring-2 ring-primary-600 bg-primary-50/20' : 'bg-white border border-slate-100'" class="flex flex-col items-center justify-center rounded-2xl p-4 transition text-center shadow-sm">
                                    <div class="mb-2 flex h-10 w-10 items-center justify-center rounded-xl bg-slate-50 text-slate-400">
                                        <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 5v14M5 12h14"></path></svg>
                                    </div>
                                    <h4 class="text-[11px] font-extrabold text-slate-900" x-text="addon.nama"></h4>
                                    <p class="mt-0.5 text-[10px] font-extrabold text-primary-600" x-text="'Rp ' + parseFloat(addon.harga).toLocaleString('id-ID')"></p>
                                </button>
                            </template>
                        </div>
                    </div>
                </div>
            </template>

            {{-- Self Service Mode Content --}}
            <template x-if="service === 'self_service'">
                <div class="space-y-6 flex flex-col h-full">
                    <div class="flex-1 rounded-3xl bg-white p-8 shadow-sm ring-1 ring-slate-100 overflow-y-auto">
                        <div class="mb-6 flex items-center justify-between">
                            <h3 class="text-sm font-extrabold text-slate-900">4. Pilih Mesin</h3>
                            <div class="flex gap-4">
                                <div class="flex items-center gap-1.5"><div class="h-2 w-2 rounded-full bg-emerald-500"></div><span class="text-[10px] font-bold text-slate-400">Tersedia</span></div>
                                <div class="flex items-center gap-1.5"><div class="h-2 w-2 rounded-full bg-amber-500"></div><span class="text-[10px] font-bold text-slate-400">Digunakan</span></div>
                                <div class="flex items-center gap-1.5"><div class="h-2 w-2 rounded-full bg-rose-500"></div><span class="text-[10px] font-bold text-slate-400">Maintenance</span></div>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 gap-4 sm:grid-cols-4">
                            <template x-for="machine in filteredMachines" :key="machine.id">
                                <button @click="toggleMachine(machine)" :disabled="machine.status !== 'AVAILABLE'" :class="[
                                    machine.status === 'AVAILABLE' ? (selectedMachines.find(m => m.id === machine.id) ? 'ring-2 ring-primary-600 bg-primary-50/20' : 'hover:ring-1 hover:ring-primary-200') : 'opacity-50 grayscale cursor-not-allowed',
                                ]" class="flex flex-col items-center justify-center rounded-[24px] bg-white border border-slate-100 p-4 transition text-center shadow-sm h-48">
                                    <div class="mb-3 flex h-12 w-12 items-center justify-center rounded-2xl bg-slate-50 text-slate-400">
                                        <svg x-show="machine.machine_type === 'WASHER'" class="h-8 w-8" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="6" y="3" width="12" height="18" rx="2"></rect><circle cx="12" cy="14" r="3"></circle></svg>
                                        <svg x-show="machine.machine_type === 'DRYER'" class="h-8 w-8" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 2v20M5 7l7 5 7-5M5 17l7-5 7 5"></path></svg>
                                    </div>
                                    <h4 class="text-sm font-extrabold text-slate-900" x-text="machine.machine_code"></h4>
                                    <p class="text-[9px] font-bold text-slate-400" x-text="machine.machine_type === 'WASHER' ? 'Washer ' + parseInt(machine.capacity_kg) + 'kg' : 'Dryer ' + parseInt(machine.capacity_kg) + 'kg'"></p>
                                    <p class="mt-1 text-[10px] font-extrabold text-slate-600" x-text="'Rp ' + (parseFloat(machine.durations.find(d => d.duration_type === (machine.machine_type === 'WASHER' ? 'WASH' : 'DRY'))?.price || machine.durations[0]?.price || 0)).toLocaleString('id-ID') + '/30m'"></p>
                                    
                                    <div class="mt-3 flex items-center gap-1.5">
                                        <div class="h-1.5 w-1.5 rounded-full" :class="machine.status === 'AVAILABLE' ? 'bg-emerald-500' : (machine.status === 'IN_USE' ? 'bg-amber-500' : 'bg-rose-500')"></div>
                                        <span class="text-[9px] font-bold text-slate-400" x-text="machine.status === 'AVAILABLE' ? 'Tersedia' : (machine.status === 'IN_USE' ? 'Digunakan' : 'Maintenance')"></span>
                                    </div>
                                </button>
                            </template>
                        </div>
                    </div>

                    {{-- Selection Summary (Bottom of Middle) --}}
                    <div x-show="selectedMachines.length > 0" class="rounded-[28px] bg-white p-6 shadow-soft ring-1 ring-slate-100">
                        <div class="flex items-center justify-between">
                            <div class="flex gap-4">
                                <template x-for="sm in selectedMachines" :key="sm.id">
                                    <div class="flex items-center gap-4 rounded-2xl bg-slate-50 p-3 ring-1 ring-slate-100">
                                        <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-white text-primary-600 shadow-sm">
                                            <svg x-show="sm.machine_type === 'WASHER'" class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="6" y="3" width="12" height="18" rx="2"></rect><circle cx="12" cy="14" r="3"></circle></svg>
                                            <svg x-show="sm.machine_type === 'DRYER'" class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 2v20M5 7l7 5 7-5M5 17l7-5 7 5"></path></svg>
                                        </div>
                                        <div>
                                            <p class="text-[10px] font-extrabold uppercase tracking-widest text-slate-300">Mesin terpilih</p>
                                            <h4 class="text-sm font-extrabold text-slate-900" x-text="sm.machine_code"></h4>
                                            <p class="text-[9px] font-bold text-slate-400" x-text="sm.machine_type === 'WASHER' ? 'Washer ' + parseInt(sm.capacity_kg) + 'kg' : 'Dryer ' + parseInt(sm.capacity_kg) + 'kg'"></p>
                                        </div>
                                    </div>
                                </template>
                            </div>
                            <button @click="selectedMachines = []" class="text-xs font-extrabold text-primary-600 hover:underline">Ubah Pilihan</button>
                        </div>
                    </div>
                </div>
            </template>
        </div>

        {{-- Column 3: Order Summary & Payment (3/12) --}}
        <div class="lg:col-span-3 space-y-6">
            {{-- Discount & Tax Inputs --}}
            <div class="rounded-3xl bg-white p-6 shadow-sm ring-1 ring-slate-100">
                <h3 class="mb-4 text-sm font-extrabold text-slate-900">Discount & Tax</h3>
                <div class="grid grid-cols-2 gap-4">
                    <div class="space-y-2">
                        <label class="text-[10px] font-extrabold uppercase tracking-widest text-slate-400">Discount (%)</label>
                        <div class="relative">
                            <input type="number" x-model="discountPercent" class="block w-full rounded-xl border-slate-100 bg-slate-50 py-2.5 px-4 text-xs font-bold text-slate-900 focus:ring-2 focus:ring-primary-500/20">
                            <div class="absolute inset-y-0 right-0 flex items-center pr-4">
                                <span class="text-[10px] font-bold text-slate-400 uppercase">%</span>
                            </div>
                        </div>
                    </div>
                    <div class="space-y-2">
                        <label class="text-[10px] font-extrabold uppercase tracking-widest text-slate-400">Tax (%)</label>
                        <div class="relative">
                            <input type="number" x-model="taxPercent" class="block w-full rounded-xl border-slate-100 bg-slate-50 py-2.5 px-4 text-xs font-bold text-slate-900 focus:ring-2 focus:ring-primary-500/20">
                            <div class="absolute inset-y-0 right-0 flex items-center pr-4">
                                <span class="text-[10px] font-bold text-slate-400 uppercase">%</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- 7. Ringkasan Pesanan --}}
            <div class="rounded-3xl bg-white p-6 shadow-sm ring-1 ring-slate-100">
                <h3 class="mb-6 text-sm font-extrabold text-slate-900">7. Ringkasan Pesanan</h3>
                <div class="space-y-4">
                    <div class="flex justify-between">
                        <span class="text-xs font-bold text-slate-400">Jenis Layanan</span>
                        <span class="rounded-lg bg-indigo-50 px-2.5 py-1 text-[10px] font-extrabold uppercase text-indigo-600" x-text="service === 'self_service' ? 'Self Service' : 'Drop Off'"></span>
                    </div>

                    {{-- Self Service Detail --}}
                    <template x-if="service === 'self_service'">
                        <div class="flex justify-between">
                            <span class="text-xs font-bold text-slate-400">Mesin</span>
                            <div class="text-right">
                                <template x-for="sm in selectedMachines" :key="sm.id">
                                    <p class="text-[11px] font-extrabold text-slate-900" x-text="sm.machine_code + ' - ' + (sm.machine_type === 'WASHER' ? 'Washer' : 'Dryer')"></p>
                                </template>
                            </div>
                        </div>
                    </template>

                    {{-- Drop Off Detail --}}
                    <template x-if="service === 'drop_off'">
                        <div class="space-y-3">
                            <div class="flex justify-between">
                                <span class="text-xs font-bold text-slate-400">Paket</span>
                                <div class="text-right">
                                    <template x-for="d in dropOff.details" :key="d.package_id">
                                        <p class="text-[11px] font-extrabold text-slate-900" x-text="(allServices.find(s => s.id === d.package_id)?.nama_paket || '-') + ' (' + d.weight + ' Kg)'"></p>
                                    </template>
                                </div>
                            </div>
                            {{-- Weight and Paket Summary combined above --}}
                            <template x-if="dropOff.selectedAddons.length > 0">
                                <div class="flex justify-between border-t border-slate-50 pt-3">
                                    <span class="text-xs font-bold text-slate-400">Add Ons</span>
                                    <div class="text-right">
                                        <template x-for="ad in dropOff.selectedAddons" :key="ad.id">
                                            <p class="text-[10px] font-extrabold text-slate-700" x-text="ad.nama"></p>
                                        </template>
                                    </div>
                                </div>
                            </template>
                        </div>
                    </template>

                    <div class="flex justify-between">
                        <span class="text-xs font-bold text-slate-400">Pelanggan</span>
                        <span class="text-xs font-extrabold text-indigo-600 text-right" x-text="selectedMember ? selectedMember.nama : 'Non Member'"></span>
                    </div>

                    <div class="pt-4 border-t border-slate-50 space-y-2">
                        <div class="flex justify-between text-xs font-bold">
                            <span class="text-slate-400">Subtotal</span>
                            <span class="text-slate-900" x-text="'Rp ' + subtotal.toLocaleString('id-ID')"></span>
                        </div>
                        <div class="flex justify-between text-xs font-bold pt-1">
                            <span class="text-slate-400">Discount (<span x-text="discountPercent"></span>%)</span>
                            <span class="text-rose-500">- Rp <span x-text="discountAmount.toLocaleString('id-ID')"></span></span>
                        </div>
                        <div class="flex justify-between text-xs font-bold pt-1">
                            <span class="text-slate-400">Tax (<span x-text="taxPercent"></span>%)</span>
                            <span class="text-slate-900">+ Rp <span x-text="taxAmount.toLocaleString('id-ID')"></span></span>
                        </div>
                    </div>
                    <div class="flex items-end justify-between pt-4 border-t border-slate-100">
                        <h4 class="text-sm font-extrabold text-slate-900">Total Bayar</h4>
                        <h4 class="text-2xl font-extrabold text-indigo-600" x-text="'Rp ' + totalAmount.toLocaleString('id-ID')"></h4>
                    </div>

                    {{-- Change Preview for Cash --}}
                    <template x-if="paymentMethod === 'CASH' && amountReceived >= totalAmount">
                        <div class="flex items-center justify-between mt-2 pt-2 border-t border-dashed border-slate-100 animate-in fade-in zoom-in duration-300">
                            <span class="text-[10px] font-extrabold text-emerald-500 uppercase tracking-widest">Kembalian</span>
                            <span class="text-sm font-extrabold text-emerald-600" x-text="'Rp ' + changeAmount.toLocaleString('id-ID')"></span>
                        </div>
                    </template>
                </div>
            </div>

            {{-- 6. Pembayaran --}}
            <div class="rounded-3xl bg-white p-6 shadow-sm ring-1 ring-slate-100">
                <h3 class="mb-4 text-sm font-extrabold text-slate-900">6. Pembayaran</h3>
                <p class="mb-4 text-[11px] font-bold text-slate-400">Metode Pembayaran</p>
                
                <div class="grid grid-cols-3 gap-3 mb-4">
                    <button @click="paymentMethod = 'CASH'" :class="paymentMethod === 'CASH' ? 'ring-2 ring-primary-600 bg-primary-50/50' : 'bg-slate-50'" class="flex flex-col items-center justify-center rounded-2xl py-3 transition">
                        <svg class="mb-1.5 h-5 w-5 text-primary-600" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="4" width="20" height="16" rx="2"></rect><circle cx="12" cy="12" r="3"></circle></svg>
                        <span class="text-[10px] font-extrabold text-slate-900">Tunai</span>
                    </button>
                    <button @click="paymentMethod = 'MEMBER_BALANCE'" :class="paymentMethod === 'MEMBER_BALANCE' ? 'ring-2 ring-primary-600 bg-primary-50/50' : 'bg-slate-50'" class="flex flex-col items-center justify-center rounded-2xl py-3 transition">
                        <svg class="mb-1.5 h-5 w-5 text-primary-600" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M16 21v-2a4 4 0 0 0-8 0v2"></path><circle cx="12" cy="7" r="4"></circle></svg>
                        <span class="text-[10px] font-extrabold text-slate-900">Saldo Member</span>
                    </button>
                    <button @click="paymentMethod = 'QRIS'" :class="paymentMethod === 'QRIS' ? 'ring-2 ring-primary-600 bg-primary-50/50' : 'bg-slate-50'" class="flex flex-col items-center justify-center rounded-2xl py-3 transition">
                        <svg class="mb-1.5 h-5 w-5 text-primary-600" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="7" height="7"></rect><rect x="14" y="3" width="7" height="7"></rect><rect x="14" y="14" width="7" height="7"></rect><rect x="3" y="14" width="7" height="7"></rect></svg>
                        <span class="text-[10px] font-extrabold text-slate-900">QRIS</span>
                    </button>
                </div>
                
                <button class="mb-6 flex w-full items-center justify-center gap-2 rounded-2xl bg-slate-50 py-3 text-[10px] font-extrabold text-slate-600">
                    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="5" width="18" height="14" rx="2"></rect><line x1="3" y1="10" x2="21" y2="10"></line></svg>
                    EDC / Card
                </button>

                <div x-show="paymentMethod === 'MEMBER_BALANCE'" class="mb-6 flex items-center justify-between rounded-2xl bg-slate-50 px-4 py-3">
                    <div>
                        <p class="text-[10px] font-bold text-slate-400">Saldo Member</p>
                        <p class="text-sm font-extrabold text-emerald-600" x-text="'Rp ' + (selectedMember ? parseFloat(selectedMember.saldo).toLocaleString('id-ID') : '0')"></p>
                    </div>
                    <button class="text-slate-400 hover:text-primary-600"><svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M23 4v6h-6M1 20v-6h6"></path><path d="M3.51 9a9 9 0 0 1 14.85-3.36L23 10M1 14l4.64 4.36A9 9 0 0 0 20.49 15"></path></svg></button>
                </div>

                <div x-show="paymentMethod === 'CASH'" class="mb-6 space-y-2">
                    <label class="text-[10px] font-extrabold uppercase tracking-widest text-slate-400">Uang Tunai Diterima</label>
                    <input type="number" x-model="amountReceived" class="block w-full rounded-xl border-slate-100 bg-slate-50 py-2.5 px-4 text-sm font-extrabold text-slate-900 focus:ring-2 focus:ring-primary-500/20">
                </div>

                <form action="{{ route('kasir.store') }}" method="POST">
                    @csrf
                    <input type="hidden" name="service_type" :value="service.toUpperCase()">
                    <input type="hidden" name="member_id" :value="selectedMember?.id">
                    <input type="hidden" name="machine_ids" :value="selectedMachines.map(m => m.id).join(',')">
                    <input type="hidden" name="payment_method" :value="paymentMethod">
                    <input type="hidden" name="amount_received" :value="paymentMethod === 'CASH' ? amountReceived : totalAmount">
                    <input type="hidden" name="discount_percent" :value="discountPercent">
                    <input type="hidden" name="discount_amount" :value="discountAmount">
                    <input type="hidden" name="tax_percent" :value="taxPercent">
                    <input type="hidden" name="tax_amount" :value="taxAmount">
                    <input type="hidden" name="total_amount" :value="totalAmount">
                    
                    {{-- Drop Off Hidden Data --}}
                    <input type="hidden" name="drop_off_details" :value="JSON.stringify(dropOff.details)">
                    <input type="hidden" name="addon_ids" :value="dropOff.selectedAddons.map(a => a.id).join(',')">
                    <input type="hidden" name="items" :value="JSON.stringify(dropOff.items)">
                    <input type="hidden" name="note" :value="dropOff.catatan">
                    <input type="hidden" name="estimated_finish" :value="dropOff.estimasiSelesai">
                    
                    <button type="submit" class="w-full rounded-2xl bg-primary-600 py-4 text-sm font-extrabold text-white shadow-xl shadow-primary-500/25 transition hover:bg-primary-700 active:scale-[0.98]" :disabled="(service === 'self_service' && selectedMachines.length === 0) || (service === 'drop_off' && dropOff.details.some(d => !d.package_id)) || (paymentMethod === 'CASH' && amountReceived < totalAmount)">
                        <div class="flex items-center justify-center gap-2">
                            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><polyline points="20 6 9 17 4 12"></polyline></svg>
                            Proses Pembayaran
                        </div>
                    </button>
                </form>
                <p class="mt-3 text-center text-[9px] font-bold text-slate-400">Mesin akan aktif setelah pembayaran berhasil</p>
            </div>
        </div>
    </div>

    {{-- Bottom Shortcuts --}}
    <div class="fixed bottom-0 left-[280px] right-0 bg-white/80 p-3 backdrop-blur border-t border-slate-100 flex gap-4">
        <button @click="location.reload()" class="flex items-center gap-2 rounded-lg bg-emerald-50 px-4 py-2 text-[10px] font-extrabold text-emerald-600">
            <span class="rounded bg-white px-1.5 py-0.5 text-[9px] shadow-sm">F2</span> Transaksi Baru
        </button>
        <button @click="searchMember = ''" class="flex items-center gap-2 rounded-lg bg-blue-50 px-4 py-2 text-[10px] font-extrabold text-blue-600">
            <span class="rounded bg-white px-1.5 py-0.5 text-[9px] shadow-sm">F3</span> Cari Member
        </button>
        <button class="flex items-center gap-2 rounded-lg bg-orange-50 px-4 py-2 text-[10px] font-extrabold text-orange-600">
            <span class="rounded bg-white px-1.5 py-0.5 text-[9px] shadow-sm">F4</span> Diskon
        </button>
        <button class="flex items-center gap-2 rounded-lg bg-indigo-50 px-4 py-2 text-[10px] font-extrabold text-indigo-600">
            <span class="rounded bg-white px-1.5 py-0.5 text-[9px] shadow-sm">F5</span> Catatan
        </button>
        <div class="flex-1"></div>
        <button @click="selectedMachines = []; selectedMember = null" class="flex items-center gap-2 rounded-lg bg-rose-50 px-4 py-2 text-[10px] font-extrabold text-rose-600">
            <span class="rounded bg-white px-1.5 py-0.5 text-[9px] shadow-sm">Esc</span> Batal
        </button>
    </div>

    {{-- New Member Modal --}}
    <div x-show="showNewMemberModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/50 backdrop-blur-sm" x-cloak>
        <div class="bg-white rounded-[32px] w-full max-w-lg overflow-hidden shadow-2xl ring-1 ring-slate-100" @click.away="showNewMemberModal = false">
            <div class="px-8 py-6 border-b border-slate-50 flex items-center justify-between">
                <h3 class="text-xl font-extrabold text-slate-900">Add New Member</h3>
                <button @click="showNewMemberModal = false" class="text-slate-400 hover:text-slate-600">
                    <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
                </button>
            </div>
            <div class="p-8 space-y-6">
                <div class="space-y-2">
                    <label class="text-xs font-extrabold uppercase tracking-widest text-slate-400">Full Name</label>
                    <input type="text" x-model="newMember.nama" class="block w-full rounded-xl border-slate-100 bg-slate-50 py-3 text-sm font-bold text-slate-900">
                </div>
                <div class="space-y-2">
                    <label class="text-xs font-extrabold uppercase tracking-widest text-slate-400">Phone Number</label>
                    <input type="text" x-model="newMember.no_hp" class="block w-full rounded-xl border-slate-100 bg-slate-50 py-3 text-sm font-bold text-slate-900">
                </div>
                <div class="space-y-2">
                    <label class="text-xs font-extrabold uppercase tracking-widest text-slate-400">Email (Optional)</label>
                    <input type="email" x-model="newMember.email" class="block w-full rounded-xl border-slate-100 bg-slate-50 py-3 text-sm font-bold text-slate-900">
                </div>
                <div class="pt-4 flex gap-3">
                    <button type="button" @click="showNewMemberModal = false" class="flex-1 rounded-xl border-2 border-slate-100 px-6 py-3.5 text-sm font-extrabold text-slate-600 transition hover:bg-slate-50">Cancel</button>
                    <button type="button" @click="saveMember()" class="flex-1 rounded-xl bg-primary-600 px-6 py-3.5 text-sm font-extrabold text-white shadow-lg shadow-primary-500/25 transition hover:bg-primary-700">Save & Select</button>
                </div>
            </div>
        </div>
    </div>

    {{-- Success Transaction Modal --}}
    @if(session('last_transaction'))
    <div x-data="{ show: true }" x-show="show" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/50 backdrop-blur-sm">
        <div class="bg-white rounded-[40px] w-full max-w-md overflow-hidden shadow-2xl ring-1 ring-slate-100 p-10 text-center" @click.away="show = false">
            <div class="mx-auto mb-6 flex h-20 w-20 items-center justify-center rounded-full bg-emerald-50 text-emerald-500">
                <svg class="h-10 w-10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3">
                    <polyline points="20 6 9 17 4 12"></polyline>
                </svg>
            </div>
            <h3 class="text-2xl font-extrabold text-slate-900">Pembayaran Berhasil!</h3>
            <p class="mt-2 text-sm font-bold text-slate-400">Transaksi {{ session('last_transaction')['number'] }}</p>
            
            <div class="mt-8 rounded-3xl bg-slate-50 p-6 space-y-4">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-bold text-slate-400 uppercase tracking-widest">Total Bayar</span>
                    <span class="text-lg font-extrabold text-slate-900">Rp {{ number_format(session('last_transaction')['total'], 0, ',', '.') }}</span>
                </div>
                <div class="flex items-center justify-between border-t border-slate-200 pt-4">
                    <span class="text-xs font-bold text-slate-400 uppercase tracking-widest">Kembalian</span>
                    <span class="text-2xl font-extrabold text-emerald-600">Rp {{ number_format(session('last_transaction')['change'], 0, ',', '.') }}</span>
                </div>
            </div>

            <div class="mt-10 flex flex-col gap-3">
                <a href="{{ route('kasir.receipt', session('last_transaction')['id']) }}" target="_blank" class="flex items-center justify-center gap-3 w-full rounded-2xl bg-primary-600 py-4 text-sm font-extrabold text-white shadow-xl shadow-primary-500/25 transition hover:bg-primary-700">
                    <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M6 9V2h12v7"></path>
                        <path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"></path>
                        <rect x="6" y="14" width="12" height="8"></rect>
                    </svg>
                    Cetak Struk
                </a>
                <button @click="show = false" class="w-full rounded-2xl border-2 border-slate-100 py-4 text-sm font-extrabold text-slate-500 transition hover:bg-slate-50">
                    Selesai
                </button>
            </div>
        </div>
    </div>
    @endif
</div>

<style>
    [x-cloak] { display: none !important; }
    body { background-color: #f8fafc; }
</style>
@endsection
