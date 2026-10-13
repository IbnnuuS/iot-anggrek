<div x-data="firebaseRealtime('{{ $firebaseUrl }}', {{ json_encode($monitoring) }}, {{ json_encode($thresholds) }})"
    x-init="init()" class="space-y-6">
    {{-- Header & Connection Bar --}}
    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-2xl font-bold tracking-tight text-zinc-900 dark:text-zinc-100">Monitoring Tanaman Anggrek
            </h1>
        </div>

        <div class="flex flex-wrap items-center gap-3">
            {{-- Firebase Connection Badge --}}
            <div class="inline-flex items-center gap-2 rounded-full border px-3 py-1 text-xs font-medium" :class="connected
                    ? 'border-emerald-500/30 bg-emerald-50 text-emerald-700 dark:bg-emerald-950/40 dark:text-emerald-400'
                    : 'border-rose-500/30 bg-rose-50 text-rose-700 dark:bg-rose-950/40 dark:text-rose-400'">
                <span class="relative flex h-2 w-2">
                    <span x-show="connected"
                        class="absolute inline-flex h-full w-full animate-ping rounded-full bg-emerald-400 opacity-75"></span>
                    <span class="relative inline-flex h-2 w-2 rounded-full"
                        :class="connected ? 'bg-emerald-500' : 'bg-rose-500'"></span>
                </span>
                <span x-text="connected ? 'Firebase Connected' : 'Reconnecting...'"></span>
            </div>
        </div>
    </div>

    {{-- Status Banner: Pompa & Kondisi Lahan --}}
    <div class="grid grid-cols-1 gap-4 lg:grid-cols-2">
        {{-- Status Pompa --}}
        <div class="rounded-xl border p-4 shadow-sm transition" :class="isPumpActive
                ? 'border-blue-500 bg-gradient-to-r from-blue-50 to-indigo-50 dark:from-blue-950/40 dark:to-indigo-950/30'
                : 'border-zinc-200 bg-white dark:border-zinc-800 dark:bg-zinc-900'">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="flex h-11 w-11 items-center justify-center rounded-lg"
                        :class="isPumpActive ? 'bg-blue-600 text-white animate-pulse' : 'bg-zinc-100 text-zinc-600 dark:bg-zinc-800 dark:text-zinc-400'">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z" />
                        </svg>
                    </div>
                    <div>
                        <div class="text-xs font-semibold uppercase tracking-wider text-zinc-500">Status Pompa
                            Penyiraman</div>
                        <div class="text-lg font-bold text-zinc-900 dark:text-zinc-100"
                            x-text="data.status_pompa || 'STANDBY'"></div>
                    </div>
                </div>
                <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-semibold" :class="isPumpActive
                        ? 'bg-blue-600 text-white animate-bounce'
                        : 'bg-zinc-100 text-zinc-700 dark:bg-zinc-800 dark:text-zinc-300'"
                    x-text="isPumpActive ? 'MENYIRAM' : 'STANDBY'">
                </span>
            </div>
        </div>

        {{-- Kondisi Lahan --}}
        <div
            class="rounded-xl border p-4 shadow-sm transition border-zinc-200 bg-white dark:border-zinc-800 dark:bg-zinc-900">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="flex h-11 w-11 items-center justify-center rounded-lg"
                        :class="isAbnormal ? 'bg-amber-500/10 text-amber-600 dark:text-amber-400' : 'bg-emerald-500/10 text-emerald-600 dark:text-emerald-400'">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                        </svg>
                    </div>
                    <div>
                        <div class="text-xs font-semibold uppercase tracking-wider text-zinc-500">Evaluasi Parameter
                            Lahan</div>
                        <div class="text-lg font-bold text-zinc-900 dark:text-zinc-100"
                            x-text="data.kondisi_lahan || 'Normal'"></div>
                    </div>
                </div>
                <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-semibold" :class="isAbnormal
                        ? 'bg-amber-100 text-amber-800 dark:bg-amber-950 dark:text-amber-300'
                        : 'bg-emerald-100 text-emerald-800 dark:bg-emerald-950 dark:text-emerald-300'"
                    x-text="isAbnormal ? 'Anomali' : 'Ideal'">
                </span>
            </div>
        </div>
    </div>

    {{-- 1. SENSOR LINGKUNGAN (DHT22 & BH1750) --}}
    <div>
        <div class="mb-3 flex items-center justify-between">
            <h2 class="text-sm font-semibold uppercase tracking-wider text-zinc-500 dark:text-zinc-400">1. Lingkungan &
                Pencahayaan (DHT22 & BH1750)</h2>
        </div>
        <div class="grid grid-cols-1 gap-4 md:grid-cols-3">
            {{-- Suhu Udara --}}
            <div class="rounded-xl border border-zinc-200 bg-white p-5 shadow-sm dark:border-zinc-800 dark:bg-zinc-900">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-medium text-zinc-500 dark:text-zinc-400">Suhu Udara</span>
                    <span class="rounded-full px-2 py-0.5 text-xs font-medium" :class="(data.suhu_udara >= (th.air_temp_min ?? 21) && data.suhu_udara <= (th.air_temp_max ?? 30))
                            ? 'bg-emerald-50 text-emerald-700 dark:bg-emerald-950/40 dark:text-emerald-400'
                            : 'bg-rose-50 text-rose-700 dark:bg-rose-950/40 dark:text-rose-400'"
                        x-text="(data.suhu_udara >= (th.air_temp_min ?? 21) && data.suhu_udara <= (th.air_temp_max ?? 30)) ? 'Normal' : 'Abnormal'">
                    </span>
                </div>
                <div class="mt-3 flex items-baseline gap-2">
                    <span class="text-3xl font-extrabold tracking-tight text-zinc-900 dark:text-zinc-100"
                        x-text="formatDec(data.suhu_udara)"></span>
                    <span class="text-lg font-medium text-zinc-500">°C</span>
                </div>
                <div class="mt-2 text-xs text-zinc-400"
                    x-text="'Ambang batas: ' + (th.air_temp_min ?? 21) + '°C – ' + (th.air_temp_max ?? 30) + '°C'">
                </div>
            </div>

            {{-- Kelembapan Udara --}}
            <div class="rounded-xl border border-zinc-200 bg-white p-5 shadow-sm dark:border-zinc-800 dark:bg-zinc-900">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-medium text-zinc-500 dark:text-zinc-400">Kelembapan Udara</span>
                    <span class="rounded-full px-2 py-0.5 text-xs font-medium" :class="(data.lembap_udara >= (th.air_hum_min ?? 60) && data.lembap_udara <= (th.air_hum_max ?? 80))
                            ? 'bg-emerald-50 text-emerald-700 dark:bg-emerald-950/40 dark:text-emerald-400'
                            : 'bg-rose-50 text-rose-700 dark:bg-rose-950/40 dark:text-rose-400'"
                        x-text="(data.lembap_udara >= (th.air_hum_min ?? 60) && data.lembap_udara <= (th.air_hum_max ?? 80)) ? 'Normal' : 'Abnormal'">
                    </span>
                </div>
                <div class="mt-3 flex items-baseline gap-2">
                    <span class="text-3xl font-extrabold tracking-tight text-zinc-900 dark:text-zinc-100"
                        x-text="formatDec(data.lembap_udara)"></span>
                    <span class="text-lg font-medium text-zinc-500">%</span>
                </div>
                <div class="mt-2 text-xs text-zinc-400"
                    x-text="'Ambang batas: ' + (th.air_hum_min ?? 60) + '% – ' + (th.air_hum_max ?? 80) + '%'"></div>
            </div>

            {{-- Intensitas Cahaya (BH1750) --}}
            <div class="rounded-xl border border-zinc-200 bg-white p-5 shadow-sm dark:border-zinc-800 dark:bg-zinc-900">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-medium text-zinc-500 dark:text-zinc-400">Intensitas Cahaya</span>
                    <span class="rounded-full px-2 py-0.5 text-xs font-medium" :class="((data.lux ?? data.cahaya ?? 0) >= (th.lux_min ?? 10000) && (data.lux ?? data.cahaya ?? 0) <= (th.lux_max ?? 20000))
                            ? 'bg-emerald-50 text-emerald-700 dark:bg-emerald-950/40 dark:text-emerald-400'
                            : 'bg-rose-50 text-rose-700 dark:bg-rose-950/40 dark:text-rose-400'"
                        x-text="((data.lux ?? data.cahaya ?? 0) >= (th.lux_min ?? 10000) && (data.lux ?? data.cahaya ?? 0) <= (th.lux_max ?? 20000)) ? 'Normal' : 'Abnormal'">
                    </span>
                </div>
                <div class="mt-3 flex items-baseline gap-2">
                    <span class="text-3xl font-extrabold tracking-tight text-zinc-900 dark:text-zinc-100"
                        x-text="formatLux(data.lux ?? data.cahaya)"></span>
                    <span class="text-lg font-medium text-zinc-500">lx</span>
                </div>
                <div class="mt-2 text-xs text-zinc-400"
                    x-text="'Ambang batas: ' + formatLux(th.lux_min ?? 10000) + ' – ' + formatLux(th.lux_max ?? 20000) + ' lx'">
                </div>
            </div>
        </div>
    </div>

    {{-- 2. KONDISI FISIK TANAH (CWT 7-IN-1) --}}
    <div>
        <div class="mb-3 flex items-center justify-between">
            <h2 class="text-sm font-semibold uppercase tracking-wider text-zinc-500 dark:text-zinc-400">2. Kondisi Fisik
                Tanah</h2>
        </div>
        <div class="grid grid-cols-2 gap-4 lg:grid-cols-4">
            {{-- Kelembapan Tanah --}}
            <div class="rounded-xl border border-zinc-200 bg-white p-4 shadow-sm dark:border-zinc-800 dark:bg-zinc-900">
                <div class="text-xs font-medium text-zinc-500 dark:text-zinc-400">Kelembapan Tanah</div>
                <div class="mt-2 flex items-baseline gap-1">
                    <span class="text-2xl font-bold tracking-tight text-zinc-900 dark:text-zinc-100"
                        x-text="formatDec(data.lembap_tanah)"></span>
                    <span class="text-sm text-zinc-500">%</span>
                </div>
                <div class="mt-2 text-[11px] text-zinc-400">Kadar air media</div>
            </div>

            {{-- Suhu Tanah --}}
            <div class="rounded-xl border border-zinc-200 bg-white p-4 shadow-sm dark:border-zinc-800 dark:bg-zinc-900">
                <div class="text-xs font-medium text-zinc-500 dark:text-zinc-400">Suhu Tanah</div>
                <div class="mt-2 flex items-baseline gap-1">
                    <span class="text-2xl font-bold tracking-tight text-zinc-900 dark:text-zinc-100"
                        x-text="formatDec(data.suhu_tanah)"></span>
                    <span class="text-sm text-zinc-500">°C</span>
                </div>
                <div class="mt-2 text-[11px] text-zinc-400">Suhu perakaran</div>
            </div>

            {{-- Derajat pH --}}
            <div class="rounded-xl border border-zinc-200 bg-white p-4 shadow-sm dark:border-zinc-800 dark:bg-zinc-900">
                <div class="text-xs font-medium text-zinc-500 dark:text-zinc-400">Derajat pH Tanah</div>
                <div class="mt-2 flex items-baseline gap-1">
                    <span class="text-2xl font-bold tracking-tight text-zinc-900 dark:text-zinc-100"
                        x-text="formatDec(data.ph)"></span>
                    <span class="text-xs font-medium text-zinc-500"
                        x-text="data.ph < 6 ? 'Asam' : (data.ph > 7.5 ? 'Basa' : 'Netral')"></span>
                </div>
                <div class="mt-2 text-[11px] text-zinc-400">Ideal anggrek: 5.5 – 6.5</div>
            </div>

            {{-- EC / Salinitas --}}
            <div class="rounded-xl border border-zinc-200 bg-white p-4 shadow-sm dark:border-zinc-800 dark:bg-zinc-900">
                <div class="text-xs font-medium text-zinc-500 dark:text-zinc-400">EC (Konduktivitas)</div>
                <div class="mt-2 flex items-baseline gap-1">
                    <span class="text-2xl font-bold tracking-tight text-zinc-900 dark:text-zinc-100"
                        x-text="data.ec || 0"></span>
                    <span class="text-xs text-zinc-500">µs/cm</span>
                </div>
                <div class="mt-2 text-[11px] text-zinc-400">Konsentrasi ion hara</div>
            </div>
        </div>
    </div>

    {{-- 3. KANDUNGAN HARA NPK (NITROGEN, FOSFOR, KALIUM) --}}
    <div>
        <div class="mb-3 flex items-center justify-between">
            <h2 class="text-sm font-semibold uppercase tracking-wider text-zinc-500 dark:text-zinc-400">3. Status
                Nutrisi Makro (NPK)</h2>
        </div>
        <div class="grid grid-cols-1 gap-4 md:grid-cols-3">
            {{-- Nitrogen (N) --}}
            <div class="rounded-xl border border-zinc-200 bg-white p-5 shadow-sm dark:border-zinc-800 dark:bg-zinc-900">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-semibold text-zinc-500">Nitrogen (N)</span>
                    <span class="rounded-full px-2 py-0.5 text-xs font-medium" :class="(data.n >= (th.n_min ?? 100) && data.n <= (th.n_max ?? 200))
                            ? 'bg-emerald-50 text-emerald-700 dark:bg-emerald-950/40 dark:text-emerald-400'
                            : 'bg-amber-50 text-amber-700 dark:bg-amber-950/40 dark:text-amber-400'"
                        x-text="(data.n >= (th.n_min ?? 100) && data.n <= (th.n_max ?? 200)) ? 'Optimal' : (data.n < (th.n_min ?? 100) ? 'Rendah' : 'Tinggi')">
                    </span>
                </div>
                <div class="mt-3 flex items-baseline gap-1">
                    <span class="text-3xl font-extrabold tracking-tight text-zinc-900 dark:text-zinc-100"
                        x-text="data.n || 0"></span>
                    <span class="text-sm text-zinc-500">ppm (mg/kg)</span>
                </div>
                <div class="mt-3 h-2 w-full overflow-hidden rounded-full bg-zinc-100 dark:bg-zinc-800">
                    <div class="h-full bg-indigo-500 transition-all duration-500"
                        :style="'width: ' + Math.min(100, Math.round((data.n || 0) / (th.n_max || 200) * 100)) + '%'">
                    </div>
                </div>
                <div class="mt-2 flex justify-between text-[11px] text-zinc-400">
                    <span>0</span>
                    <span x-text="'Target: ' + (th.n_min ?? 100) + '-' + (th.n_max ?? 200) + ' ppm'"></span>
                    <span x-text="(th.n_max ?? 200) + '+'"></span>
                </div>
            </div>

            {{-- Fosfor (P) --}}
            <div class="rounded-xl border border-zinc-200 bg-white p-5 shadow-sm dark:border-zinc-800 dark:bg-zinc-900">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-semibold text-zinc-500">Fosfor (P)</span>
                    <span class="rounded-full px-2 py-0.5 text-xs font-medium" :class="(data.p >= (th.p_min ?? 50) && data.p <= (th.p_max ?? 100))
                            ? 'bg-emerald-50 text-emerald-700 dark:bg-emerald-950/40 dark:text-emerald-400'
                            : 'bg-amber-50 text-amber-700 dark:bg-amber-950/40 dark:text-amber-400'"
                        x-text="(data.p >= (th.p_min ?? 50) && data.p <= (th.p_max ?? 100)) ? 'Optimal' : (data.p < (th.p_min ?? 50) ? 'Rendah' : 'Tinggi')">
                    </span>
                </div>
                <div class="mt-3 flex items-baseline gap-1">
                    <span class="text-3xl font-extrabold tracking-tight text-zinc-900 dark:text-zinc-100"
                        x-text="data.p || 0"></span>
                    <span class="text-sm text-zinc-500">ppm (mg/kg)</span>
                </div>
                <div class="mt-3 h-2 w-full overflow-hidden rounded-full bg-zinc-100 dark:bg-zinc-800">
                    <div class="h-full bg-purple-500 transition-all duration-500"
                        :style="'width: ' + Math.min(100, Math.round((data.p || 0) / (th.p_max || 100) * 100)) + '%'">
                    </div>
                </div>
                <div class="mt-2 flex justify-between text-[11px] text-zinc-400">
                    <span>0</span>
                    <span x-text="'Target: ' + (th.p_min ?? 50) + '-' + (th.p_max ?? 100) + ' ppm'"></span>
                    <span x-text="(th.p_max ?? 100) + '+'"></span>
                </div>
            </div>

            {{-- Kalium (K) --}}
            <div class="rounded-xl border border-zinc-200 bg-white p-5 shadow-sm dark:border-zinc-800 dark:bg-zinc-900">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-semibold text-zinc-500">Kalium (K)</span>
                    <span class="rounded-full px-2 py-0.5 text-xs font-medium" :class="(data.k >= (th.k_min ?? 100) && data.k <= (th.k_max ?? 200))
                            ? 'bg-emerald-50 text-emerald-700 dark:bg-emerald-950/40 dark:text-emerald-400'
                            : 'bg-amber-50 text-amber-700 dark:bg-amber-950/40 dark:text-amber-400'"
                        x-text="(data.k >= (th.k_min ?? 100) && data.k <= (th.k_max ?? 200)) ? 'Optimal' : (data.k < (th.k_min ?? 100) ? 'Rendah' : 'Tinggi')">
                    </span>
                </div>
                <div class="mt-3 flex items-baseline gap-1">
                    <span class="text-3xl font-extrabold tracking-tight text-zinc-900 dark:text-zinc-100"
                        x-text="data.k || 0"></span>
                    <span class="text-sm text-zinc-500">ppm (mg/kg)</span>
                </div>
                <div class="mt-3 h-2 w-full overflow-hidden rounded-full bg-zinc-100 dark:bg-zinc-800">
                    <div class="h-full bg-teal-500 transition-all duration-500"
                        :style="'width: ' + Math.min(100, Math.round((data.k || 0) / (th.k_max || 200) * 100)) + '%'">
                    </div>
                </div>
                <div class="mt-2 flex justify-between text-[11px] text-zinc-400">
                    <span>0</span>
                    <span x-text="'Target: ' + (th.k_min ?? 100) + '-' + (th.k_max ?? 200) + ' ppm'"></span>
                    <span x-text="(th.k_max ?? 200) + '+'"></span>
                </div>
            </div>
        </div>
    </div>
</div>
</div>
</div>

<script>
    function firebaseRealtime(url, initialData, initialThresholds) {
        return {
            url: url.replace(/\/$/, ''),
            data: initialData || {},
            th: initialThresholds || {
                air_temp_min: 21,
                air_temp_max: 30,
                air_hum_min: 60,
                air_hum_max: 80,
                lux_min: 10000,
                lux_max: 20000,
                n_min: 100,
                n_max: 200,
                p_min: 50,
                p_max: 100,
                k_min: 100,
                k_max: 200
            },
            connected: false,
            lastUpdateText: 'Memuat...',
            pollTimer: null,
            evtSource: null,

            init() {
                if (this.data && Object.keys(this.data).length > 0) {
                    this.connected = true;
                    this.lastUpdateText = new Date().toLocaleTimeString();
                }

                this.startListening();
                this.pollTimer = setInterval(() => this.fetchRest(), 3000);
            },

            startListening() {
                try {
                    const sseUrl = `${this.url}/monitoring.json`;
                    this.evtSource = new EventSource(sseUrl);

                    this.evtSource.addEventListener('put', (e) => {
                        try {
                            const payload = JSON.parse(e.data);
                            if (payload.path === '/' && payload.data) {
                                this.data = Object.assign({}, this.data, payload.data);
                            } else if (payload.path) {
                                const key = payload.path.replace('/', '');
                                this.data[key] = payload.data;
                            }
                            this.connected = true;
                            this.lastUpdateText = new Date().toLocaleTimeString();
                        } catch (err) { }
                    });

                    this.evtSource.onerror = () => {
                        this.connected = false;
                    };
                } catch (e) {
                    this.fetchRest();
                }
            },

            async fetchRest() {
                try {
                    const res = await fetch(`${this.url}/monitoring.json`, {
                        cache: 'no-store'
                    });
                    if (res.ok) {
                        const json = await res.json();
                        if (json) {
                            this.data = Object.assign({}, this.data, json);
                            this.connected = true;
                            this.lastUpdateText = new Date().toLocaleTimeString();
                        }
                    }
                } catch (e) {
                    this.connected = false;
                }

                try {
                    const thRes = await fetch(`${this.url}/thresholds.json`, {
                        cache: 'no-store'
                    });
                    if (thRes.ok) {
                        const thJson = await thRes.json();
                        if (thJson) {
                            this.th = Object.assign({}, this.th, thJson);
                        }
                    }
                } catch (e) { }
            },

            formatDec(val) {
                if (val === undefined || val === null || isNaN(val)) return '0.0';
                return parseFloat(val).toFixed(1);
            },

            formatLux(val) {
                if (val === undefined || val === null || isNaN(val)) return '0';
                return Math.round(Number(val)).toLocaleString('id-ID');
            },

            get isPumpActive() {
                const st = (this.data.status_pompa || '').toUpperCase();
                return st.includes('AKTIF') || st.includes('MENYIRAM') || st.includes('ON');
            },

            get isAbnormal() {
                const cond = (this.data.kondisi_lahan || '').toUpperCase();
                return cond.includes('ABNORMAL') || cond.includes('PERLU');
            }
        }
    }
</script>
