<h3>Jadwal Guru</h3>
<p>Aktifkan hari presensi dan atur batas masuk serta jam pulang.</p>
    <div class="table-wrap"><table>
        <thead><tr><th>Hari</th><th>Status</th><th>Batas Masuk</th><th>Mulai Pulang</th></tr></thead>
        <tbody>
        @foreach ($hari as $nomor => $nama)
            <tr>
                <td>{{ $nama }}</td>
                <td><select aria-label="Status {{ $nama }}" name="jadwal[{{ $nomor }}][aktif]">
                    <option value="0">Nonaktif</option>
                    <option value="1" @selected(old("jadwal.$nomor.aktif", $jadwal[$nomor]['aktif'] ?? false))>Aktif</option>
                </select></td>
                <td><input aria-label="Batas masuk {{ $nama }}" type="time" name="jadwal[{{ $nomor }}][jam_masuk]" value="{{ old("jadwal.$nomor.jam_masuk", $jadwal[$nomor]['jam_masuk'] ?? '07:30') }}" required></td>
                <td><input aria-label="Mulai pulang {{ $nama }}" type="time" name="jadwal[{{ $nomor }}][jam_pulang]" value="{{ old("jadwal.$nomor.jam_pulang", $jadwal[$nomor]['jam_pulang'] ?? '12:00') }}" required></td>
            </tr>
        @endforeach
        </tbody>
    </table></div>
