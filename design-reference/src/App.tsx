import { useState, useEffect } from "react"
import {
  BarChart, Bar, XAxis, YAxis, CartesianGrid, Tooltip, ResponsiveContainer, Cell, ReferenceLine
} from "recharts"

// ─── Types ────────────────────────────────────────────────────────────────────
type Role = "superAdmin" | "komiteMutu" | "kepalaUnit"
type MenuKey = "dashboard" | "logbook" | "kelola-user" | "kelola-profil"
type PillarKey = "INM" | "IMP-RS" | "IMU"

// ─── Mock Data ────────────────────────────────────────────────────────────────
const UNITS = [
  "Farmasi", "IGD", "ICU", "Laboratorium", "Radiologi",
  "Rawat Inap A", "Rawat Inap B", "Bedah Sentral", "Gizi", "CSSD"
]

const unitProgress = [
  { unit: "ICU",          achieved: 43, target: 85, filled: 6,  total: 12 },
  { unit: "IGD",          achieved: 51, target: 85, filled: 8,  total: 14 },
  { unit: "Bedah Sentral",achieved: 55, target: 85, filled: 7,  total: 11 },
  { unit: "Radiologi",    achieved: 61, target: 85, filled: 9,  total: 12 },
  { unit: "CSSD",         achieved: 67, target: 85, filled: 8,  total: 10 },
  { unit: "Laboratorium", achieved: 72, target: 85, filled: 10, total: 12 },
  { unit: "Gizi",         achieved: 74, target: 85, filled: 9,  total: 11 },
  { unit: "Rawat Inap B", achieved: 79, target: 85, filled: 11, total: 13 },
  { unit: "Rawat Inap A", achieved: 82, target: 85, filled: 12, total: 14 },
  { unit: "Farmasi",      achieved: 91, target: 85, filled: 14, total: 14 },
]

const logbookData = [
  { no: 1,  name: "Kepatuhan Identifikasi Pasien",        type: "INM",    target: 100, numerator: 142, denominator: 145, result: 97.9, achieved: 97.9, deadline: "2 hari",  status: "yellow" },
  { no: 2,  name: "Emergency Response Time IGD ≤5 menit", type: "INM",    target: 80,  numerator: 81,  denominator: 95,  result: 85.3, achieved: 85.3, deadline: "5 hari",  status: "green"  },
  { no: 3,  name: "Kepatuhan Hand Hygiene",                type: "INM",    target: 85,  numerator: 60,  denominator: 90,  result: 66.7, achieved: 66.7, deadline: "0 hari",  status: "red"    },
  { no: 4,  name: "Angka Phlebitis",                       type: "IMP-RS", target: 1.5, numerator: 3,   denominator: 400, result: 0.75, achieved: 0.75, deadline: "3 hari",  status: "green"  },
  { no: 5,  name: "Waktu Tunggu Farmasi Rawat Jalan",      type: "IMU",    target: 90,  numerator: 44,  denominator: 50,  result: 88.0, achieved: 88.0, deadline: "4 hari",  status: "green"  },
  { no: 6,  name: "Kepatuhan Penggunaan APD",              type: "INM",    target: 100, numerator: 48,  denominator: 60,  result: 80.0, achieved: 80.0, deadline: "1 hari",  status: "yellow" },
  { no: 7,  name: "Kejadian Salah Pemberian Obat",         type: "IMP-RS", target: 0,   numerator: 1,   denominator: 1200,result: 0.08, achieved: 0.08, deadline: "6 hari",  status: "green"  },
  { no: 8,  name: "Kepuasan Pasien",                       type: "IMU",    target: 80,  numerator: 76,  denominator: 100, result: 76.0, achieved: 76.0, deadline: "0 hari",  status: "red"    },
  { no: 9,  name: "Kelengkapan Rekam Medis 24 Jam",        type: "INM",    target: 100, numerator: 88,  denominator: 92,  result: 95.7, achieved: 95.7, deadline: "3 hari",  status: "green"  },
  { no: 10, name: "Angka Infeksi Saluran Kemih (ISK)",     type: "IMP-RS", target: 0,   numerator: 0,   denominator: 150, result: 0,    achieved: 0,    deadline: "5 hari",  status: "green"  },
  { no: 11, name: "Waktu Pelaporan Kritis Lab",            type: "INM",    target: 100, numerator: 30,  denominator: 35,  result: 85.7, achieved: 85.7, deadline: "2 hari",  status: "yellow" },
  { no: 12, name: "Penundaan Operasi Elektif",             type: "IMP-RS", target: 5,   numerator: 4,   denominator: 120, result: 3.3,  achieved: 3.3,  deadline: "1 hari",  status: "yellow" },
]

const allUsers = [
  { id: 1,  name: "dr. Rina Kusuma, Sp.PD",    unit: "Komite Mutu",  role: "komiteMutu", lastLogin: "17 Jul 2026 08:22", status: "Aktif"     },
  { id: 2,  name: "Apt. Budi Santoso",          unit: "Farmasi",      role: "kepalaUnit", lastLogin: "17 Jul 2026 09:11", status: "Aktif"     },
  { id: 3,  name: "Ns. Sari Dewi, S.Kep",      unit: "ICU",          role: "kepalaUnit", lastLogin: "16 Jul 2026 14:05", status: "Aktif"     },
  { id: 4,  name: "dr. Hendra Wijaya",          unit: "IGD",          role: "kepalaUnit", lastLogin: "17 Jul 2026 07:45", status: "Aktif"     },
  { id: 5,  name: "Rahma Putri, S.Gz",          unit: "Gizi",         role: "kepalaUnit", lastLogin: "15 Jul 2026 11:30", status: "Nonaktif"  },
  { id: 6,  name: "Anisa Mahardika, S.Kep",    unit: "Rawat Inap A", role: "kepalaUnit", lastLogin: "17 Jul 2026 08:58", status: "Aktif"     },
  { id: 7,  name: "Teguh Prasetyo, Amd.Rad",   unit: "Radiologi",    role: "kepalaUnit", lastLogin: "16 Jul 2026 16:20", status: "Aktif"     },
  { id: 8,  name: "dr. Fitria Nanda, Sp.An",   unit: "Bedah Sentral",role: "kepalaUnit", lastLogin: "14 Jul 2026 09:00", status: "Nonaktif"  },
]

const indicators: Record<PillarKey, Array<{ no: string; name: string; units: string[] }>> = {
  "INM": [
    { no: "INM-01", name: "Kepatuhan Identifikasi Pasien",             units: ["IGD","ICU","Rawat Inap A","Rawat Inap B","Farmasi"] },
    { no: "INM-02", name: "Emergency Response Time ≤5 menit",          units: ["IGD"] },
    { no: "INM-03", name: "Kepatuhan Hand Hygiene (5 Momen)",          units: ["IGD","ICU","Rawat Inap A","Bedah Sentral","Laboratorium"] },
    { no: "INM-04", name: "Kepatuhan Penggunaan APD",                   units: ["ICU","Bedah Sentral","Laboratorium","Radiologi"] },
    { no: "INM-05", name: "Pelaporan Hasil Kritis Laboratorium",        units: ["Laboratorium","ICU","IGD"] },
    { no: "INM-06", name: "Kelengkapan Rekam Medis 24 Jam Pasca Rawat",units: ["Rawat Inap A","Rawat Inap B","ICU","Bedah Sentral"] },
  ],
  "IMP-RS": [
    { no: "IMP-RS-01", name: "Angka Phlebitis Rawat Inap",              units: ["Rawat Inap A","Rawat Inap B","ICU"] },
    { no: "IMP-RS-02", name: "Angka Infeksi Saluran Kemih (ISK-CAUTI)", units: ["ICU","Rawat Inap A"] },
    { no: "IMP-RS-03", name: "Penundaan Operasi Elektif",               units: ["Bedah Sentral"] },
    { no: "IMP-RS-04", name: "Kejadian Salah Pemberian Obat",           units: ["Farmasi","Rawat Inap A","Rawat Inap B","ICU","IGD"] },
    { no: "IMP-RS-05", name: "Angka Dekubitus Pasien Tirah Baring",     units: ["ICU","Rawat Inap B"] },
  ],
  "IMU": [
    { no: "IMU-F-01", name: "Waktu Tunggu Farmasi Rawat Jalan ≤30 mnt",units: ["Farmasi"] },
    { no: "IMU-G-01", name: "Ketepatan Pemberian Diit",                  units: ["Gizi","Rawat Inap A","Rawat Inap B"] },
    { no: "IMU-R-01", name: "Waktu Tunggu Hasil Radiologi ≤3 Jam",      units: ["Radiologi"] },
    { no: "IMU-L-01", name: "Waktu Tunggu Hasil Lab Cito ≤60 Menit",    units: ["Laboratorium","IGD","ICU"] },
    { no: "IMU-P-01", name: "Kepuasan Pasien ≥80%",                     units: ["Farmasi","Rawat Inap A","Rawat Inap B","IGD"] },
  ],
}

const notifications = [
  { id: 1, text: "ICU belum mengisi 3 indikator bulan ini",     time: "10 mnt lalu", urgent: true  },
  { id: 2, text: "Laporan Farmasi Juli 2026 berhasil dikirim",  time: "1 jam lalu",  urgent: false },
  { id: 3, text: "Deadline pengisian data: 2 hari lagi",        time: "3 jam lalu",  urgent: true  },
  { id: 4, text: "User baru ditambahkan: Teguh Prasetyo",       time: "Kemarin",     urgent: false },
]

// ─── Helpers ──────────────────────────────────────────────────────────────────
function getBarColor(achieved: number, target: number) {
  const ratio = achieved / target
  if (ratio >= 1) return "#22c55e"
  if (ratio >= 0.75) return "#f59e0b"
  return "#ef4444"
}

function Clock() {
  const [time, setTime] = useState(new Date())
  useEffect(() => {
    const t = setInterval(() => setTime(new Date()), 1000)
    return () => clearInterval(t)
  }, [])
  return (
    <span style={{ fontFamily: "'JetBrains Mono', monospace" }} className="text-xs text-slate-500">
      {time.toLocaleDateString("id-ID", { weekday: "short", day: "2-digit", month: "short", year: "numeric" })}
      {" "}
      <span className="text-[#4274D9] font-semibold">
        {time.toLocaleTimeString("id-ID")}
      </span>
    </span>
  )
}

// ─── Sub-components ───────────────────────────────────────────────────────────

function StatusBadge({ status, label }: { status: string; label: string }) {
  const cls = status === "green" ? "badge-green" : status === "yellow" ? "badge-yellow" : "badge-red"
  return (
    <span className={`${cls} text-[10px] font-semibold px-2 py-0.5 rounded-full`}>
      {label}
    </span>
  )
}

function TypeBadge({ type }: { type: string }) {
  const colors: Record<string, string> = {
    "INM": "bg-[#293681] text-white",
    "IMP-RS": "bg-[#4274D9] text-white",
    "IMU": "bg-teal-600 text-white",
  }
  return (
    <span className={`${colors[type] ?? "bg-slate-200"} text-[10px] font-bold px-2 py-0.5 rounded`}>
      {type}
    </span>
  )
}

// ─── Dashboard: superAdmin / komiteMutu ───────────────────────────────────────
function DashboardAdmin() {
  const sorted = [...unitProgress].sort((a, b) => a.achieved - b.achieved)
  return (
    <div className="p-6 space-y-6">
      {/* Summary cards */}
      <div className="grid grid-cols-4 gap-4">
        {[
          { label: "Total Indikator Aktif", value: "21",  sub: "3 kategori",          color: "#293681" },
          { label: "Unit Memenuhi Target",  value: "4",   sub: "dari 10 unit",         color: "#22c55e" },
          { label: "Unit Dalam Proses",     value: "4",   sub: "perlu perhatian",      color: "#f59e0b" },
          { label: "Unit Kritis",           value: "2",   sub: "di bawah 60%",         color: "#ef4444" },
        ].map(c => (
          <div key={c.label} className="rounded-xl border border-[#95CCDD] bg-white p-4 shadow-sm">
            <div className="text-xs text-slate-500 font-medium mb-1">{c.label}</div>
            <div className="text-3xl font-bold" style={{ color: c.color, fontFamily: "'Plus Jakarta Sans', sans-serif" }}>{c.value}</div>
            <div className="text-xs text-slate-400 mt-0.5">{c.sub}</div>
          </div>
        ))}
      </div>

      {/* Chart section */}
      <div className="grid grid-cols-5 gap-4">
        <div className="col-span-3 bg-white rounded-xl border border-[#95CCDD] shadow-sm p-5">
          <div className="flex items-center justify-between mb-4">
            <div>
              <h3 className="font-semibold text-[#293681] text-sm" style={{ fontFamily: "'Plus Jakarta Sans', sans-serif" }}>
                Pencapaian Indikator per Unit — Juli 2026
              </h3>
              <p className="text-xs text-slate-400 mt-0.5">Diurutkan dari terendah ke tertinggi · Target: 85%</p>
            </div>
            <span className="text-[11px] bg-[#D0E7E6] text-[#293681] px-3 py-1 rounded-full font-semibold">
              Periode: Jul 2026
            </span>
          </div>
          <ResponsiveContainer width="100%" height={260}>
            <BarChart data={sorted} layout="vertical" margin={{ left: 90, right: 30, top: 0, bottom: 0 }}>
              <CartesianGrid strokeDasharray="3 3" stroke="#f0f4f8" horizontal={false} />
              <XAxis type="number" domain={[0, 100]} tickFormatter={v => `${v}%`} tick={{ fontSize: 11, fontFamily: "JetBrains Mono" }} />
              <YAxis type="category" dataKey="unit" tick={{ fontSize: 11, fontFamily: "Inter", fill: "#374151" }} width={85} />
              <Tooltip
                formatter={(v) => [`${v}%`, "Pencapaian"]}
                contentStyle={{ fontSize: 12, fontFamily: "Inter" }}
              />
              <ReferenceLine x={85} stroke="#293681" strokeDasharray="4 4" strokeWidth={1.5} label={{ value: "Target", position: "insideTopRight", fontSize: 10, fill: "#293681" }} />
              <Bar dataKey="achieved" radius={[0, 4, 4, 0]} maxBarSize={18}>
                {sorted.map((entry, i) => (
                  <Cell key={i} fill={getBarColor(entry.achieved, entry.target)} />
                ))}
              </Bar>
            </BarChart>
          </ResponsiveContainer>
        </div>

        {/* Unit detail list */}
        <div className="col-span-2 bg-white rounded-xl border border-[#95CCDD] shadow-sm p-5 overflow-auto" style={{ maxHeight: 380 }}>
          <h3 className="font-semibold text-[#293681] text-sm mb-3" style={{ fontFamily: "'Plus Jakarta Sans', sans-serif" }}>
            Detail Target & Pengisian
          </h3>
          <div className="space-y-3">
            {sorted.map(u => (
              <div key={u.unit}>
                <div className="flex justify-between items-center mb-1">
                  <span className="text-xs font-medium text-slate-700">{u.unit}</span>
                  <div className="flex items-center gap-2">
                    <span className="text-[10px] text-slate-400 font-mono">{u.filled}/{u.total} terisi</span>
                    <span className="text-[11px] font-bold" style={{ color: getBarColor(u.achieved, u.target) }}>
                      {u.achieved}%
                    </span>
                  </div>
                </div>
                <div className="progress-bar-track">
                  <div
                    className="h-full rounded-full transition-all"
                    style={{ width: `${u.achieved}%`, background: getBarColor(u.achieved, u.target) }}
                  />
                </div>
              </div>
            ))}
          </div>
        </div>
      </div>
    </div>
  )
}

// ─── Dashboard: kepalaUnit ────────────────────────────────────────────────────
function DashboardKepalaUnit() {
  const myUnit = unitProgress.find(u => u.unit === "Farmasi")!
  const pending = myUnit.total - myUnit.filled
  return (
    <div className="p-6 space-y-5">
      <div className="grid grid-cols-3 gap-4">
        {[
          { label: "Indikator Belum Diisi",  value: pending.toString(), sub: "wajib diselesaikan",   color: "#ef4444", bg: "#fee2e2" },
          { label: "Indikator Sudah Diisi",  value: myUnit.filled.toString(), sub: "dari " + myUnit.total + " total", color: "#22c55e", bg: "#dcfce7" },
          { label: "Pencapaian Unit",        value: myUnit.achieved + "%", sub: "target 85%",         color: "#293681", bg: "#D0E7E6" },
        ].map(c => (
          <div key={c.label} className="rounded-xl border border-[#95CCDD] p-5 shadow-sm flex gap-4 items-center" style={{ background: c.bg }}>
            <div>
              <div className="text-xs font-medium text-slate-600 mb-1">{c.label}</div>
              <div className="text-4xl font-black" style={{ color: c.color, fontFamily: "'Plus Jakarta Sans', sans-serif" }}>{c.value}</div>
              <div className="text-xs text-slate-500 mt-0.5">{c.sub}</div>
            </div>
          </div>
        ))}
      </div>

      {/* Countdown panel */}
      <div className="bg-white rounded-xl border border-[#95CCDD] shadow-sm p-5">
        <h3 className="font-semibold text-[#293681] text-sm mb-3" style={{ fontFamily: "'Plus Jakarta Sans', sans-serif" }}>
          Status Pengisian — Unit Farmasi
        </h3>
        <div className="space-y-2">
          {logbookData.slice(0, 6).map(row => (
            <div key={row.no} className="flex items-center gap-3 p-2 rounded-lg hover:bg-slate-50">
              <StatusBadge status={row.status} label={row.status === "green" ? "✓" : row.status === "yellow" ? "!" : "✗"} />
              <span className="text-sm text-slate-700 flex-1">{row.name}</span>
              <TypeBadge type={row.type} />
              <span className="text-xs font-mono text-slate-500">{row.achieved}%</span>
              <span className={`text-xs font-medium ${row.status === "red" ? "text-red-600" : row.status === "yellow" ? "text-amber-600" : "text-green-600"}`}>
                {row.deadline === "0 hari" ? "HARI INI" : "+" + row.deadline}
              </span>
            </div>
          ))}
        </div>
      </div>

      {/* Unit achievement bar */}
      <div className="bg-white rounded-xl border border-[#95CCDD] shadow-sm p-5">
        <h3 className="font-semibold text-[#293681] text-sm mb-4" style={{ fontFamily: "'Plus Jakarta Sans', sans-serif" }}>
          Capaian Bulanan — Unit Farmasi
        </h3>
        <ResponsiveContainer width="100%" height={150}>
          <BarChart data={[
            { bulan: "Feb", val: 72 }, { bulan: "Mar", val: 78 }, { bulan: "Apr", val: 83 },
            { bulan: "Mei", val: 80 }, { bulan: "Jun", val: 88 }, { bulan: "Jul", val: 91 },
          ]}>
            <CartesianGrid strokeDasharray="3 3" stroke="#f0f4f8" vertical={false} />
            <XAxis dataKey="bulan" tick={{ fontSize: 11 }} />
            <YAxis domain={[60, 100]} tickFormatter={v => `${v}%`} tick={{ fontSize: 10, fontFamily: "JetBrains Mono" }} />
            <Tooltip formatter={(v) => [`${v}%`, "Capaian"]} />
            <ReferenceLine y={85} stroke="#293681" strokeDasharray="4 4" label={{ value: "85%", position: "insideRight", fontSize: 10, fill: "#293681" }} />
            <Bar dataKey="val" fill="#4274D9" radius={[4, 4, 0, 0]} maxBarSize={32} />
          </BarChart>
        </ResponsiveContainer>
      </div>
    </div>
  )
}

// ─── Logbook: kepalaUnit ──────────────────────────────────────────────────────
function LogbookKepalaUnit() {
  const [editRow, setEditRow] = useState<number | null>(null)
  const [data, setData] = useState(logbookData)

  function handleEdit(no: number, field: "numerator" | "denominator", val: string) {
    setData(prev => prev.map(r => {
      if (r.no !== no) return r
      const updated = { ...r, [field]: Number(val) }
      const res = updated.denominator > 0 ? (updated.numerator / updated.denominator) * 100 : 0
      return { ...updated, result: Math.round(res * 10) / 10, achieved: Math.round(res * 10) / 10 }
    }))
  }

  return (
    <div className="p-0 flex flex-col h-full">
      {/* top bar */}
      <div className="flex items-center justify-between px-6 py-3 border-b border-[#95CCDD] bg-white sticky top-0 z-10">
        <div>
          <span className="text-xs text-slate-400">Periode:</span>
          <span className="ml-1 text-xs font-semibold text-[#293681]">Juli 2026</span>
          <span className="mx-2 text-slate-300">·</span>
          <span className="text-xs text-slate-400">Unit:</span>
          <span className="ml-1 text-xs font-semibold text-[#4274D9]">Farmasi</span>
        </div>
        <div className="flex gap-2">
          <button className="text-xs font-semibold border border-[#95CCDD] text-[#293681] px-3 py-1.5 rounded-lg hover:bg-[#D0E7E6] transition-colors">
            + Tambah Baris
          </button>
          <button
            className="text-xs font-bold text-white px-4 py-1.5 rounded-lg shadow-sm transition-all hover:opacity-90 flex items-center gap-1.5"
            style={{ background: "#4274D9" }}
          >
            <span>🖨</span> Cetak &amp; Kirim Laporan
          </button>
        </div>
      </div>

      {/* table */}
      <div className="overflow-auto flex-1">
        <table className="data-table w-full border-collapse">
          <thead>
            <tr>
              <th className="text-center w-8">No</th>
              <th className="min-w-52">Nama Indikator</th>
              <th className="text-center">Tipe</th>
              <th className="text-center">Target (%)</th>
              <th className="text-center">Tgl Otomatis</th>
              <th className="text-center">Numerator</th>
              <th className="text-center">Denominator</th>
              <th className="text-center">Hasil Harian (%)</th>
              <th className="text-center">Capaian (%)</th>
              <th className="text-center">Sisa Deadline</th>
              <th className="text-center">Status</th>
              <th className="text-center">Aksi</th>
            </tr>
          </thead>
          <tbody>
            {data.map(row => {
              const isEditing = editRow === row.no
              return (
                <tr key={row.no} className={isEditing ? "bg-blue-50" : ""}>
                  <td className="text-center text-slate-400">{row.no}</td>
                  <td className="font-sans text-[12px] text-slate-800 max-w-xs" style={{ fontFamily: "Inter" }}>
                    {row.name}
                  </td>
                  <td className="text-center"><TypeBadge type={row.type} /></td>
                  <td className="text-center">{row.target}{row.type === "IMP-RS" ? "" : "%"}</td>
                  <td className="text-center text-slate-500 text-[11px]">17 Jul 2026</td>
                  <td className="text-center">
                    {isEditing ? (
                      <input
                        className="w-16 border border-[#4274D9] rounded px-1 py-0.5 text-center text-xs outline-none"
                        defaultValue={row.numerator}
                        onChange={e => handleEdit(row.no, "numerator", e.target.value)}
                      />
                    ) : (
                      <span>{row.numerator}</span>
                    )}
                  </td>
                  <td className="text-center">
                    {isEditing ? (
                      <input
                        className="w-16 border border-[#4274D9] rounded px-1 py-0.5 text-center text-xs outline-none"
                        defaultValue={row.denominator}
                        onChange={e => handleEdit(row.no, "denominator", e.target.value)}
                      />
                    ) : (
                      <span>{row.denominator}</span>
                    )}
                  </td>
                  <td className="text-center font-semibold">{row.result}%</td>
                  <td className="text-center">
                    <span className={`font-bold ${row.status === "green" ? "text-green-600" : row.status === "yellow" ? "text-amber-500" : "text-red-600"}`}>
                      {row.achieved}%
                    </span>
                  </td>
                  <td className="text-center">
                    <span className={`text-xs font-semibold ${row.deadline === "0 hari" ? "text-red-600" : row.deadline === "1 hari" || row.deadline === "2 hari" ? "text-amber-500" : "text-slate-500"}`}>
                      {row.deadline === "0 hari" ? "HARI INI" : row.deadline}
                    </span>
                  </td>
                  <td className="text-center">
                    <StatusBadge
                      status={row.status}
                      label={row.status === "green" ? "Selesai" : row.status === "yellow" ? "Mendekati" : "Terlambat"}
                    />
                  </td>
                  <td className="text-center">
                    <div className="flex justify-center gap-1">
                      <button
                        onClick={() => setEditRow(isEditing ? null : row.no)}
                        className={`text-[10px] font-semibold px-2 py-0.5 rounded transition-colors ${isEditing ? "bg-[#293681] text-white" : "border border-[#4274D9] text-[#4274D9] hover:bg-[#D0E7E6]"}`}
                      >
                        {isEditing ? "Simpan" : "Edit"}
                      </button>
                      <button className="text-[10px] font-semibold px-2 py-0.5 rounded border border-slate-200 text-slate-500 hover:bg-slate-100 transition-colors">
                        Lihat
                      </button>
                    </div>
                  </td>
                </tr>
              )
            })}
          </tbody>
        </table>
      </div>
    </div>
  )
}

// ─── Logbook: Admin view with unit sub-sidebar ────────────────────────────────
function LogbookAdmin() {
  const [selectedUnit, setSelectedUnit] = useState("Farmasi")
  return (
    <div className="flex h-full">
      {/* Unit list sub-sidebar */}
      <div className="w-52 border-r border-[#95CCDD] bg-[#f6fbfd] flex-shrink-0 py-3">
        <div className="px-4 mb-2">
          <span className="text-[10px] font-bold text-[#293681] uppercase tracking-widest">Pilih Unit</span>
        </div>
        {UNITS.map(unit => (
          <button
            key={unit}
            onClick={() => setSelectedUnit(unit)}
            className={`w-full text-left px-4 py-2 text-xs font-medium transition-all ${
              selectedUnit === unit
                ? "bg-[#D0E7E6] text-[#293681] font-semibold border-r-2 border-[#4274D9]"
                : "text-slate-600 hover:bg-[#D0E7E6]/50"
            }`}
          >
            {unit}
          </button>
        ))}
      </div>

      {/* Report panel */}
      <div className="flex-1 p-6">
        <div className="flex items-center justify-between mb-5">
          <div>
            <h2 className="text-base font-bold text-[#293681]" style={{ fontFamily: "'Plus Jakarta Sans', sans-serif" }}>
              Laporan — {selectedUnit}
            </h2>
            <p className="text-xs text-slate-400 mt-0.5">Klik bulan untuk pratinjau atau unduh laporan</p>
          </div>
          <div className="flex gap-2">
            <button className="text-xs font-semibold border border-[#4274D9] text-[#4274D9] px-3 py-1.5 rounded-lg hover:bg-[#D0E7E6] transition-colors flex items-center gap-1">
              <span>📄</span> Unduh PDF
            </button>
            <button className="text-xs font-bold text-white px-3 py-1.5 rounded-lg shadow-sm transition-all hover:opacity-90 flex items-center gap-1" style={{ background: "#22c55e" }}>
              <span>📊</span> Unduh Excel
            </button>
          </div>
        </div>

        {/* Month grid */}
        <div className="grid grid-cols-6 gap-3 mb-6">
          {["Jan","Feb","Mar","Apr","Mei","Jun","Jul","Agu","Sep","Okt","Nov","Des"].map((m, i) => {
            const done = i < 7
            const current = i === 6
            return (
              <button
                key={m}
                className={`rounded-lg p-3 text-center transition-all border ${
                  current
                    ? "border-[#4274D9] bg-[#4274D9] text-white shadow-md"
                    : done
                    ? "border-[#95CCDD] bg-white hover:bg-[#D0E7E6] text-[#293681]"
                    : "border-slate-200 bg-slate-50 text-slate-300 cursor-not-allowed"
                }`}
              >
                <div className="text-xs font-bold">{m}</div>
                <div className="text-[10px] mt-0.5">{done ? (current ? "Aktif" : "✓") : "–"}</div>
              </button>
            )
          })}
        </div>

        {/* Preview table */}
        <div className="bg-white rounded-xl border border-[#95CCDD] shadow-sm overflow-hidden">
          <div className="px-4 py-3 border-b border-[#95CCDD] bg-[#D0E7E6] flex justify-between items-center">
            <span className="text-xs font-bold text-[#293681]">Pratinjau Laporan — {selectedUnit} — Juli 2026</span>
            <span className="text-[10px] text-slate-500">Generated: 17 Jul 2026 09:00</span>
          </div>
          <div className="overflow-auto">
            <table className="data-table w-full border-collapse">
              <thead>
                <tr>
                  <th>No</th>
                  <th>Nama Indikator</th>
                  <th>Tipe</th>
                  <th>Target</th>
                  <th>Capaian</th>
                  <th>Status</th>
                </tr>
              </thead>
              <tbody>
                {logbookData.slice(0, 6).map(row => (
                  <tr key={row.no}>
                    <td className="text-center">{row.no}</td>
                    <td style={{ fontFamily: "Inter", fontSize: 12 }}>{row.name}</td>
                    <td className="text-center"><TypeBadge type={row.type} /></td>
                    <td className="text-center">{row.target}%</td>
                    <td className="text-center">
                      <span className={`font-bold ${row.status === "green" ? "text-green-600" : row.status === "yellow" ? "text-amber-500" : "text-red-600"}`}>
                        {row.achieved}%
                      </span>
                    </td>
                    <td className="text-center">
                      <StatusBadge status={row.status} label={row.status === "green" ? "Tercapai" : row.status === "yellow" ? "Dalam Proses" : "Tidak Tercapai"} />
                    </td>
                  </tr>
                ))}
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  )
}

// ─── Kelola User ──────────────────────────────────────────────────────────────
function KelolaPengguna({ role }: { role: Role }) {
  const visible = role === "superAdmin" ? allUsers : allUsers.filter(u => u.role !== "superAdmin")
  return (
    <div className="p-6">
      <div className="flex items-center justify-between mb-5">
        <div>
          <h2 className="text-base font-bold text-[#293681]" style={{ fontFamily: "'Plus Jakarta Sans', sans-serif" }}>
            Manajemen Pengguna
          </h2>
          <p className="text-xs text-slate-400 mt-0.5">
            {role === "superAdmin" ? "Semua pengguna terdaftar" : "Pengguna non-superAdmin"}
          </p>
        </div>
        <button className="text-xs font-bold text-white px-4 py-2 rounded-lg shadow-sm hover:opacity-90 flex items-center gap-1.5" style={{ background: "#4274D9" }}>
          + Tambah Pengguna
        </button>
      </div>

      <div className="bg-white rounded-xl border border-[#95CCDD] shadow-sm overflow-hidden">
        <table className="data-table w-full border-collapse">
          <thead>
            <tr>
              <th className="text-center">No</th>
              <th>Nama Lengkap</th>
              <th>Unit / Departemen</th>
              <th>Peran</th>
              <th>Login Terakhir</th>
              <th className="text-center">Status</th>
              <th className="text-center">Log Aktivitas</th>
              <th className="text-center">Aksi</th>
            </tr>
          </thead>
          <tbody>
            {visible.map((u, i) => (
              <tr key={u.id}>
                <td className="text-center text-slate-400">{i + 1}</td>
                <td style={{ fontFamily: "Inter", fontWeight: 500 }} className="text-slate-800">{u.name}</td>
                <td className="text-slate-600">{u.unit}</td>
                <td>
                  <span className={`text-[10px] font-bold px-2 py-0.5 rounded-full ${
                    u.role === "superAdmin" ? "bg-[#293681] text-white"
                    : u.role === "komiteMutu" ? "bg-[#4274D9] text-white"
                    : "bg-[#D0E7E6] text-[#293681]"
                  }`}>
                    {u.role === "superAdmin" ? "Super Admin" : u.role === "komiteMutu" ? "Komite Mutu" : "Kepala Unit"}
                  </span>
                </td>
                <td className="text-slate-500">{u.lastLogin}</td>
                <td className="text-center">
                  <StatusBadge status={u.status === "Aktif" ? "green" : "red"} label={u.status} />
                </td>
                <td className="text-center">
                  <button className="text-[10px] text-[#4274D9] font-semibold hover:underline">Lihat Log</button>
                </td>
                <td>
                  <div className="flex justify-center gap-1">
                    <button className="text-[10px] border border-[#4274D9] text-[#4274D9] px-2 py-0.5 rounded hover:bg-[#D0E7E6] transition-colors font-semibold">Edit</button>
                    <button className="text-[10px] border border-amber-300 text-amber-600 px-2 py-0.5 rounded hover:bg-amber-50 transition-colors font-semibold">Reset PW</button>
                    {role === "superAdmin" && (
                      <button className="text-[10px] border border-red-300 text-red-500 px-2 py-0.5 rounded hover:bg-red-50 transition-colors font-semibold">Nonaktif</button>
                    )}
                  </div>
                </td>
              </tr>
            ))}
          </tbody>
        </table>
      </div>
    </div>
  )
}

// ─── Kelola Profil Indikator ──────────────────────────────────────────────────
function KelolaProfilIndikator() {
  const [pillar, setPillar] = useState<PillarKey>("INM")
  const rows = indicators[pillar]
  return (
    <div className="flex h-full">
      {/* Pillar sub-sidebar */}
      <div className="w-52 border-r border-[#95CCDD] bg-[#f6fbfd] flex-shrink-0 py-4">
        <div className="px-4 mb-3">
          <span className="text-[10px] font-bold text-[#293681] uppercase tracking-widest">Kategori Indikator</span>
        </div>
        {(["INM", "IMP-RS", "IMU"] as PillarKey[]).map(p => (
          <button
            key={p}
            onClick={() => setPillar(p)}
            className={`w-full text-left px-4 py-3 text-xs transition-all ${
              pillar === p
                ? "bg-[#D0E7E6] text-[#293681] font-bold border-r-2 border-[#4274D9]"
                : "text-slate-600 hover:bg-[#D0E7E6]/50 font-medium"
            }`}
          >
            <div className="font-bold text-sm">{p}</div>
            <div className="text-[10px] text-slate-500 mt-0.5">
              {p === "INM" ? "Indikator Nasional Mutu" : p === "IMP-RS" ? "Indikator Mutu Prioritas RS" : "Indikator Mutu Unit"}
            </div>
          </button>
        ))}
      </div>

      {/* Indicator table */}
      <div className="flex-1 p-6">
        <div className="flex items-center justify-between mb-4">
          <div>
            <h2 className="text-base font-bold text-[#293681]" style={{ fontFamily: "'Plus Jakarta Sans', sans-serif" }}>
              {pillar === "INM" ? "Indikator Nasional Mutu (INM)" : pillar === "IMP-RS" ? "Indikator Mutu Prioritas RS (IMP-RS)" : "Indikator Mutu Unit (IMU)"}
            </h2>
            <p className="text-xs text-slate-400 mt-0.5">{rows.length} indikator terdaftar</p>
          </div>
          <button className="text-xs font-bold text-white px-4 py-2 rounded-lg shadow-sm hover:opacity-90" style={{ background: "#4274D9" }}>
            + Tambah Indikator
          </button>
        </div>

        <div className="bg-white rounded-xl border border-[#95CCDD] shadow-sm overflow-hidden">
          <table className="data-table w-full border-collapse">
            <thead>
              <tr>
                <th className="text-center w-24">No. Indikator</th>
                <th>Nama Indikator</th>
                <th>Daftar Unit Pelaksana</th>
                <th className="text-center">Aksi</th>
              </tr>
            </thead>
            <tbody>
              {rows.map(row => (
                <tr key={row.no}>
                  <td className="text-center">
                    <span className="font-mono font-bold text-[#4274D9] text-[11px]">{row.no}</span>
                  </td>
                  <td style={{ fontFamily: "Inter", fontSize: 13 }} className="text-slate-800 font-medium">{row.name}</td>
                  <td>
                    <div className="flex flex-wrap gap-1">
                      {row.units.map(u => (
                        <span key={u} className="text-[10px] bg-[#D0E7E6] text-[#293681] px-2 py-0.5 rounded-full font-medium">{u}</span>
                      ))}
                    </div>
                  </td>
                  <td>
                    <div className="flex justify-center gap-1">
                      <button className="text-[10px] border border-[#4274D9] text-[#4274D9] px-2 py-0.5 rounded hover:bg-[#D0E7E6] transition-colors font-semibold">Edit</button>
                      <button className="text-[10px] border border-red-300 text-red-500 px-2 py-0.5 rounded hover:bg-red-50 transition-colors font-semibold">Hapus</button>
                    </div>
                  </td>
                </tr>
              ))}
            </tbody>
          </table>
        </div>
      </div>
    </div>
  )
}

// ─── Root App ─────────────────────────────────────────────────────────────────
export default function App() {
  const [role, setRole] = useState<Role>("superAdmin")
  const [menu, setMenu] = useState<MenuKey>("dashboard")
  const [showNotif, setShowNotif] = useState(false)
  const [search, setSearch] = useState("")

  const menuItems: Array<{ key: MenuKey; icon: string; label: string; roles: Role[] }> = [
    { key: "dashboard",     icon: "◈",  label: "Dashboard",                  roles: ["superAdmin","komiteMutu","kepalaUnit"] },
    { key: "logbook",       icon: "⊞",  label: "Logbook",                    roles: ["superAdmin","komiteMutu","kepalaUnit"] },
    { key: "kelola-user",   icon: "◎",  label: "Kelola Pengguna",            roles: ["superAdmin","komiteMutu"] },
    { key: "kelola-profil", icon: "◫",  label: "Kelola Profil Indikator",    roles: ["superAdmin","komiteMutu"] },
  ]

  const activeLabel: Record<MenuKey, string> = {
    "dashboard":     "Dashboard",
    "logbook":       role === "kepalaUnit" ? "Logbook — Unit Farmasi" : "Logbook",
    "kelola-user":   "Kelola Pengguna",
    "kelola-profil": "Kelola Profil Indikator Mutu",
  }

  const userInfo: Record<Role, { name: string; pos: string; initials: string }> = {
    "superAdmin": { name: "Administrator",       pos: "Super Admin",      initials: "SA" },
    "komiteMutu": { name: "dr. Rina Kusuma",    pos: "Komite Mutu",      initials: "RK" },
    "kepalaUnit": { name: "Apt. Budi Santoso",  pos: "Kepala Unit Farmasi", initials: "BS" },
  }

  const user = userInfo[role]

  function renderContent() {
    switch (menu) {
      case "dashboard":
        return role === "kepalaUnit" ? <DashboardKepalaUnit /> : <DashboardAdmin />
      case "logbook":
        return role === "kepalaUnit" ? <LogbookKepalaUnit /> : <LogbookAdmin />
      case "kelola-user":
        return <KelolaPengguna role={role} />
      case "kelola-profil":
        return <KelolaProfilIndikator />
      default:
        return null
    }
  }

  const roleBadgeColor = role === "superAdmin" ? "#293681" : role === "komiteMutu" ? "#4274D9" : "#0d9488"
  const roleLabel = role === "superAdmin" ? "Super Admin" : role === "komiteMutu" ? "Komite Mutu" : "Kepala Unit"

  return (
    <div className="flex flex-col h-screen overflow-hidden bg-[#f8fafc]">
      {/* ── Dev: Role switcher ── */}
      <div className="flex justify-center gap-2 py-1.5 text-[10px] bg-slate-800 text-slate-300 z-50">
        <span className="text-slate-500">Demo Role:</span>
        {(["superAdmin","komiteMutu","kepalaUnit"] as Role[]).map(r => (
          <button
            key={r}
            onClick={() => { setRole(r); setMenu("dashboard") }}
            className={`px-2 py-0.5 rounded font-semibold transition-colors ${role === r ? "bg-[#4274D9] text-white" : "hover:text-white"}`}
          >
            {r}
          </button>
        ))}
      </div>

      <div className="flex flex-1 overflow-hidden">
        {/* ── SIDEBAR ── */}
        <aside className="w-56 flex-shrink-0 flex flex-col" style={{ background: "#293681" }}>
          {/* Brand */}
          <div className="px-5 pt-5 pb-4 border-b border-white/10">
            <div className="flex items-center gap-3">
              <div className="w-9 h-9 rounded-lg bg-white/15 flex items-center justify-center text-white font-black text-base" style={{ fontFamily: "'Plus Jakarta Sans', sans-serif" }}>
                ✦
              </div>
              <div>
                <div className="text-white font-bold text-[13px] leading-tight" style={{ fontFamily: "'Plus Jakarta Sans', sans-serif" }}>SIMUTURS</div>
                <div className="text-white/50 text-[9px] font-medium tracking-wide leading-tight">Sistem Indikator Mutu RS</div>
              </div>
            </div>
          </div>

          {/* Nav */}
          <nav className="flex-1 py-4 px-2 space-y-0.5">
            <div className="px-3 mb-2">
              <span className="text-[9px] font-bold text-white/30 uppercase tracking-widest">Menu Utama</span>
            </div>
            {menuItems.filter(m => m.roles.includes(role)).map(item => (
              <button
                key={item.key}
                onClick={() => setMenu(item.key)}
                className={`sidebar-item w-full flex items-center gap-3 px-3 py-2.5 rounded-lg text-left transition-all ${
                  menu === item.key ? "active" : ""
                }`}
              >
                <span className="text-lg leading-none" style={{ color: menu === item.key ? "#95CCDD" : "rgba(255,255,255,0.5)" }}>
                  {item.icon}
                </span>
                <span className={`text-[12px] font-medium ${menu === item.key ? "text-white" : "text-white/70"}`}>
                  {item.label}
                </span>
              </button>
            ))}
          </nav>

          {/* User profile */}
          <div className="px-4 py-4 border-t border-white/10">
            <div className="flex items-center gap-3">
              <div
                className="w-9 h-9 rounded-full flex items-center justify-center text-xs font-bold text-white flex-shrink-0"
                style={{ background: roleBadgeColor }}
              >
                {user.initials}
              </div>
              <div className="min-w-0">
                <div className="text-white text-[11px] font-semibold truncate">{user.name}</div>
                <div className="text-white/50 text-[10px] truncate">{user.pos}</div>
              </div>
            </div>
          </div>
        </aside>

        {/* ── MAIN AREA ── */}
        <div className="flex-1 flex flex-col overflow-hidden">
          {/* ── NAVBAR ── */}
          <header className="h-12 flex items-center justify-between px-5 border-b border-[#95CCDD] bg-white flex-shrink-0">
            {/* Left: Title */}
            <div className="flex items-center gap-2">
              <h1 className="text-[13px] font-bold text-[#293681]" style={{ fontFamily: "'Plus Jakarta Sans', sans-serif" }}>
                {activeLabel[menu]}
              </h1>
              <span className="text-slate-300">|</span>
              <Clock />
            </div>

            {/* Right: Search + badges */}
            <div className="flex items-center gap-3">
              {/* Search */}
              <div className="nav-search flex items-center gap-1.5 bg-[#f6fbfd] border border-[#95CCDD] rounded-lg px-3 py-1.5">
                <svg className="w-3 h-3 text-slate-400" fill="none" stroke="currentColor" strokeWidth={2} viewBox="0 0 24 24">
                  <circle cx="11" cy="11" r="8" /><path d="m21 21-4.35-4.35" />
                </svg>
                <input
                  className="bg-transparent text-xs text-slate-600 placeholder-slate-400 w-40"
                  placeholder="Cari menu atau indikator..."
                  value={search}
                  onChange={e => setSearch(e.target.value)}
                />
              </div>

              {/* Role badge */}
              <span
                className="text-[10px] font-bold text-white px-2.5 py-1 rounded-full"
                style={{ background: roleBadgeColor }}
              >
                {roleLabel}
              </span>

              {/* Notification bell */}
              <div className="relative">
                <button
                  onClick={() => setShowNotif(n => !n)}
                  className="relative w-8 h-8 flex items-center justify-center rounded-lg hover:bg-[#D0E7E6] transition-colors"
                >
                  <svg className="w-4 h-4 text-[#293681]" fill="none" stroke="currentColor" strokeWidth={2} viewBox="0 0 24 24">
                    <path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9" />
                    <path d="M13.73 21a2 2 0 0 1-3.46 0" />
                  </svg>
                  <span className="absolute top-1 right-1 w-2 h-2 rounded-full bg-red-500 live-dot" />
                </button>

                {showNotif && (
                  <div className="absolute right-0 top-10 w-80 bg-white rounded-xl shadow-xl border border-[#95CCDD] z-50 overflow-hidden">
                    <div className="px-4 py-3 border-b border-[#95CCDD] bg-[#D0E7E6]">
                      <span className="text-xs font-bold text-[#293681]">Notifikasi</span>
                    </div>
                    {notifications
                      .filter(n => role === "kepalaUnit" ? !n.text.includes("User baru") : true)
                      .map(n => (
                        <div key={n.id} className={`px-4 py-3 border-b border-slate-100 hover:bg-slate-50 cursor-pointer flex gap-3 items-start`}>
                          <span className={`mt-0.5 w-2 h-2 rounded-full flex-shrink-0 ${n.urgent ? "bg-red-400" : "bg-slate-300"}`} />
                          <div>
                            <p className="text-xs text-slate-700">{n.text}</p>
                            <p className="text-[10px] text-slate-400 mt-0.5">{n.time}</p>
                          </div>
                        </div>
                      ))
                    }
                  </div>
                )}
              </div>
            </div>
          </header>

          {/* ── CONTENT ── */}
          <main className="flex-1 overflow-auto">
            {renderContent()}
          </main>

          {/* ── FOOTER ── */}
          <footer className="h-8 flex items-center justify-between px-6 border-t border-[#95CCDD] bg-white flex-shrink-0">
            <span className="text-[10px] text-slate-400">
              <span className="font-semibold text-[#293681]">SIMUTURS</span> — Sistem Informasi Manajemen Mutu Rumah Sakit
            </span>
            <span className="text-[10px] text-slate-400">
              © 2026 · Dikembangkan oleh <span className="font-semibold">Tim IT Rumah Sakit</span>
            </span>
          </footer>
        </div>
      </div>

      {/* Click-away for notification */}
      {showNotif && (
        <div className="fixed inset-0 z-40" onClick={() => setShowNotif(false)} />
      )}
    </div>
  )
}
