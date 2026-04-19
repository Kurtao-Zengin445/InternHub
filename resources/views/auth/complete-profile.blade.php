<x-guest-layout>
    @php
        $selectedRole = $user->role;
        $meta = $roleMeta[$selectedRole];
    @endphp

    <div class="mb-8">
        <div class="inline-flex items-center gap-2 rounded-full bg-emerald-50 px-3 py-1 text-xs font-semibold uppercase tracking-[0.2em] text-emerald-700">
            <span class="h-2 w-2 rounded-full bg-emerald-500"></span>
            Lengkapi Profil
        </div>
        <h1 class="mt-4 text-3xl font-bold tracking-tight text-slate-950" style="font-family:'Syne',sans-serif;">
            Selesaikan Registrasi {{ $meta['label'] }}
        </h1>
        <p class="mt-2 text-sm text-slate-600">
            Akun Google Anda sudah terhubung. Lengkapi data {{ strtolower($meta['label']) }} berikut agar fitur sesuai role bisa langsung digunakan.
        </p>
    </div>

    <form method="POST" action="{{ route('register.complete.store') }}">
        @csrf
        <input type="hidden" name="role" value="{{ $selectedRole }}">

        <div class="grid gap-5 sm:grid-cols-2">
            @if ($selectedRole === 'student')
                <div class="sm:col-span-2">
                    <x-input-label for="name" class="text-sm font-semibold text-slate-700" :value="__('Nama Lengkap')" />
                    <x-text-input id="name" class="mt-2 block w-full rounded-2xl border-slate-300 px-4 py-3 text-sm shadow-sm focus:border-blue-500 focus:ring-blue-500" type="text" name="name" :value="old('name', $user->name)" required autofocus autocomplete="name" />
                    <x-input-error :messages="$errors->get('name')" class="mt-2" />
                </div>

                <div class="sm:col-span-2">
                    <x-input-label for="email" class="text-sm font-semibold text-slate-700" :value="__('Email')" />
                    <x-text-input id="email" class="mt-2 block w-full rounded-2xl border-slate-300 px-4 py-3 text-sm shadow-sm focus:border-blue-500 focus:ring-blue-500" type="email" name="email" :value="old('email', $user->email)" required autocomplete="username" />
                    <x-input-error :messages="$errors->get('email')" class="mt-2" />
                </div>

                <div class="sm:col-span-2">
                    <x-input-label for="school_id" class="text-sm font-semibold text-slate-700" :value="__('Sekolah')" />
                    <select id="school_id" name="school_id" class="mt-2 block w-full rounded-2xl border-slate-300 px-4 py-3 text-sm shadow-sm focus:border-blue-500 focus:ring-blue-500" required>
                        <option value="">Pilih sekolah</option>
                        @foreach($schools as $school)
                            <option value="{{ $school->id }}" {{ (string) old('school_id') === (string) $school->id ? 'selected' : '' }}>
                                {{ $school->name }}
                            </option>
                        @endforeach
                    </select>
                    <x-input-error :messages="$errors->get('school_id')" class="mt-2" />
                </div>

                <div>
                    <x-input-label for="nis" class="text-sm font-semibold text-slate-700" :value="__('NIS')" />
                    <x-text-input id="nis" class="mt-2 block w-full rounded-2xl border-slate-300 px-4 py-3 text-sm shadow-sm focus:border-blue-500 focus:ring-blue-500" type="text" name="nis" :value="old('nis')" required />
                    <x-input-error :messages="$errors->get('nis')" class="mt-2" />
                </div>

                <div>
                    <x-input-label for="class" class="text-sm font-semibold text-slate-700" :value="__('Kelas')" />
                    <x-text-input id="class" class="mt-2 block w-full rounded-2xl border-slate-300 px-4 py-3 text-sm shadow-sm focus:border-blue-500 focus:ring-blue-500" type="text" name="class" :value="old('class')" required />
                    <x-input-error :messages="$errors->get('class')" class="mt-2" />
                </div>

                <div class="sm:col-span-2">
                    <x-input-label for="major" class="text-sm font-semibold text-slate-700" :value="__('Jurusan')" />
                    <x-text-input id="major" class="mt-2 block w-full rounded-2xl border-slate-300 px-4 py-3 text-sm shadow-sm focus:border-blue-500 focus:ring-blue-500" type="text" name="major" :value="old('major')" required />
                    <x-input-error :messages="$errors->get('major')" class="mt-2" />
                </div>

                <div>
                    <x-input-label for="gender" class="text-sm font-semibold text-slate-700" :value="__('Jenis Kelamin')" />
                    <select id="gender" name="gender" class="mt-2 block w-full rounded-2xl border-slate-300 px-4 py-3 text-sm shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        <option value="">Pilih jenis kelamin</option>
                        <option value="male" {{ old('gender') === 'male' ? 'selected' : '' }}>Laki-laki</option>
                        <option value="female" {{ old('gender') === 'female' ? 'selected' : '' }}>Perempuan</option>
                    </select>
                    <x-input-error :messages="$errors->get('gender')" class="mt-2" />
                </div>

                <div>
                    <x-input-label for="birth_place" class="text-sm font-semibold text-slate-700" :value="__('Tempat Lahir')" />
                    <x-text-input id="birth_place" class="mt-2 block w-full rounded-2xl border-slate-300 px-4 py-3 text-sm shadow-sm focus:border-blue-500 focus:ring-blue-500" type="text" name="birth_place" :value="old('birth_place')" />
                    <x-input-error :messages="$errors->get('birth_place')" class="mt-2" />
                </div>

                <div>
                    <x-input-label for="birth_date" class="text-sm font-semibold text-slate-700" :value="__('Tanggal Lahir')" />
                    <x-text-input id="birth_date" class="mt-2 block w-full rounded-2xl border-slate-300 px-4 py-3 text-sm shadow-sm focus:border-blue-500 focus:ring-blue-500" type="date" name="birth_date" :value="old('birth_date')" />
                    <x-input-error :messages="$errors->get('birth_date')" class="mt-2" />
                </div>

                <div>
                    <x-input-label for="phone" class="text-sm font-semibold text-slate-700" :value="__('Nomor Telepon')" />
                    <x-text-input id="phone" class="mt-2 block w-full rounded-2xl border-slate-300 px-4 py-3 text-sm shadow-sm focus:border-blue-500 focus:ring-blue-500" type="text" name="phone" :value="old('phone')" />
                    <x-input-error :messages="$errors->get('phone')" class="mt-2" />
                </div>

                <div class="sm:col-span-2">
                    <x-input-label for="address" class="text-sm font-semibold text-slate-700" :value="__('Alamat')" />
                    <textarea id="address" name="address" rows="3" class="mt-2 block w-full rounded-2xl border-slate-300 px-4 py-3 text-sm shadow-sm focus:border-blue-500 focus:ring-blue-500">{{ old('address') }}</textarea>
                    <x-input-error :messages="$errors->get('address')" class="mt-2" />
                </div>
            @elseif ($selectedRole === 'school')
                <div class="sm:col-span-2">
                    <x-input-label for="name" class="text-sm font-semibold text-slate-700" :value="__('Nama Pengelola Akun')" />
                    <x-text-input id="name" class="mt-2 block w-full rounded-2xl border-slate-300 px-4 py-3 text-sm shadow-sm focus:border-emerald-500 focus:ring-emerald-500" type="text" name="name" :value="old('name', $user->name)" required autofocus autocomplete="name" />
                    <x-input-error :messages="$errors->get('name')" class="mt-2" />
                </div>

                <div class="sm:col-span-2">
                    <x-input-label for="email" class="text-sm font-semibold text-slate-700" :value="__('Email Login')" />
                    <x-text-input id="email" class="mt-2 block w-full rounded-2xl border-slate-300 px-4 py-3 text-sm shadow-sm focus:border-emerald-500 focus:ring-emerald-500" type="email" name="email" :value="old('email', $user->email)" required autocomplete="username" />
                    <x-input-error :messages="$errors->get('email')" class="mt-2" />
                </div>

                <div class="sm:col-span-2">
                    <x-input-label for="institution_name" class="text-sm font-semibold text-slate-700" :value="__('Nama Sekolah')" />
                    <x-text-input id="institution_name" class="mt-2 block w-full rounded-2xl border-slate-300 px-4 py-3 text-sm shadow-sm focus:border-emerald-500 focus:ring-emerald-500" type="text" name="institution_name" :value="old('institution_name')" required />
                    <x-input-error :messages="$errors->get('institution_name')" class="mt-2" />
                </div>

                <div>
                    <x-input-label for="npsn" class="text-sm font-semibold text-slate-700" :value="__('NPSN')" />
                    <x-text-input id="npsn" class="mt-2 block w-full rounded-2xl border-slate-300 px-4 py-3 text-sm shadow-sm focus:border-emerald-500 focus:ring-emerald-500" type="text" name="npsn" :value="old('npsn')" />
                    <x-input-error :messages="$errors->get('npsn')" class="mt-2" />
                </div>

                <div>
                    <x-input-label for="school_type" class="text-sm font-semibold text-slate-700" :value="__('Jenis Sekolah')" />
                    <x-text-input id="school_type" class="mt-2 block w-full rounded-2xl border-slate-300 px-4 py-3 text-sm shadow-sm focus:border-emerald-500 focus:ring-emerald-500" type="text" name="school_type" :value="old('school_type')" placeholder="SMK, SMA, MA" />
                    <x-input-error :messages="$errors->get('school_type')" class="mt-2" />
                </div>

                <div class="sm:col-span-2">
                    <x-input-label for="principal_name" class="text-sm font-semibold text-slate-700" :value="__('Nama Kepala Sekolah')" />
                    <x-text-input id="principal_name" class="mt-2 block w-full rounded-2xl border-slate-300 px-4 py-3 text-sm shadow-sm focus:border-emerald-500 focus:ring-emerald-500" type="text" name="principal_name" :value="old('principal_name')" />
                    <x-input-error :messages="$errors->get('principal_name')" class="mt-2" />
                </div>

                <div>
                    <x-input-label for="phone" class="text-sm font-semibold text-slate-700" :value="__('Nomor Telepon')" />
                    <x-text-input id="phone" class="mt-2 block w-full rounded-2xl border-slate-300 px-4 py-3 text-sm shadow-sm focus:border-emerald-500 focus:ring-emerald-500" type="text" name="phone" :value="old('phone')" />
                    <x-input-error :messages="$errors->get('phone')" class="mt-2" />
                </div>

                <div>
                    <x-input-label for="institution_email" class="text-sm font-semibold text-slate-700" :value="__('Email Sekolah')" />
                    <x-text-input id="institution_email" class="mt-2 block w-full rounded-2xl border-slate-300 px-4 py-3 text-sm shadow-sm focus:border-emerald-500 focus:ring-emerald-500" type="email" name="institution_email" :value="old('institution_email')" />
                    <x-input-error :messages="$errors->get('institution_email')" class="mt-2" />
                </div>

                <div class="sm:col-span-2">
                    <x-input-label for="address" class="text-sm font-semibold text-slate-700" :value="__('Alamat Sekolah')" />
                    <textarea id="address" name="address" rows="3" class="mt-2 block w-full rounded-2xl border-slate-300 px-4 py-3 text-sm shadow-sm focus:border-emerald-500 focus:ring-emerald-500" required>{{ old('address') }}</textarea>
                    <x-input-error :messages="$errors->get('address')" class="mt-2" />
                </div>
            @elseif ($selectedRole === 'supervisor')
                <div class="sm:col-span-2">
                    <x-input-label for="name" class="text-sm font-semibold text-slate-700" :value="__('Nama Lengkap Pembimbing')" />
                    <x-text-input id="name" class="mt-2 block w-full rounded-2xl border-slate-300 px-4 py-3 text-sm shadow-sm focus:border-amber-500 focus:ring-amber-500" type="text" name="name" :value="old('name', $user->name)" required autofocus autocomplete="name" />
                    <x-input-error :messages="$errors->get('name')" class="mt-2" />
                </div>

                <div class="sm:col-span-2">
                    <x-input-label for="email" class="text-sm font-semibold text-slate-700" :value="__('Email Login')" />
                    <x-text-input id="email" class="mt-2 block w-full rounded-2xl border-slate-300 px-4 py-3 text-sm shadow-sm focus:border-amber-500 focus:ring-amber-500" type="email" name="email" :value="old('email', $user->email)" required autocomplete="username" />
                    <x-input-error :messages="$errors->get('email')" class="mt-2" />
                </div>

                <div class="sm:col-span-2">
                    <x-input-label for="school_id" class="text-sm font-semibold text-slate-700" :value="__('Asal Sekolah')" />
                    <select id="school_id" name="school_id" class="mt-2 block w-full rounded-2xl border-slate-300 px-4 py-3 text-sm shadow-sm focus:border-amber-500 focus:ring-amber-500" required>
                        <option value="">Pilih sekolah</option>
                        @foreach($schools as $school)
                            <option value="{{ $school->id }}" {{ (string) old('school_id') === (string) $school->id ? 'selected' : '' }}>
                                {{ $school->name }}
                            </option>
                        @endforeach
                    </select>
                    <x-input-error :messages="$errors->get('school_id')" class="mt-2" />
                </div>

                <div>
                    <x-input-label for="nip" class="text-sm font-semibold text-slate-700" :value="__('NIP')" />
                    <x-text-input id="nip" class="mt-2 block w-full rounded-2xl border-slate-300 px-4 py-3 text-sm shadow-sm focus:border-amber-500 focus:ring-amber-500" type="text" name="nip" :value="old('nip')" />
                    <x-input-error :messages="$errors->get('nip')" class="mt-2" />
                </div>

                <div>
                    <x-input-label for="position" class="text-sm font-semibold text-slate-700" :value="__('Jabatan')" />
                    <x-text-input id="position" class="mt-2 block w-full rounded-2xl border-slate-300 px-4 py-3 text-sm shadow-sm focus:border-amber-500 focus:ring-amber-500" type="text" name="position" :value="old('position')" placeholder="Guru Produktif RPL" />
                    <x-input-error :messages="$errors->get('position')" class="mt-2" />
                </div>

                <div class="sm:col-span-2">
                    <x-input-label for="phone" class="text-sm font-semibold text-slate-700" :value="__('Nomor Telepon')" />
                    <x-text-input id="phone" class="mt-2 block w-full rounded-2xl border-slate-300 px-4 py-3 text-sm shadow-sm focus:border-amber-500 focus:ring-amber-500" type="text" name="phone" :value="old('phone')" />
                    <x-input-error :messages="$errors->get('phone')" class="mt-2" />
                </div>
            @else
                <div class="sm:col-span-2">
                    <x-input-label for="name" class="text-sm font-semibold text-slate-700" :value="__('Nama Pengelola Akun')" />
                    <x-text-input id="name" class="mt-2 block w-full rounded-2xl border-slate-300 px-4 py-3 text-sm shadow-sm focus:border-violet-500 focus:ring-violet-500" type="text" name="name" :value="old('name', $user->name)" required autofocus autocomplete="name" />
                    <x-input-error :messages="$errors->get('name')" class="mt-2" />
                </div>

                <div class="sm:col-span-2">
                    <x-input-label for="email" class="text-sm font-semibold text-slate-700" :value="__('Email Login')" />
                    <x-text-input id="email" class="mt-2 block w-full rounded-2xl border-slate-300 px-4 py-3 text-sm shadow-sm focus:border-violet-500 focus:ring-violet-500" type="email" name="email" :value="old('email', $user->email)" required autocomplete="username" />
                    <x-input-error :messages="$errors->get('email')" class="mt-2" />
                </div>

                <div class="sm:col-span-2">
                    <x-input-label for="company_name" class="text-sm font-semibold text-slate-700" :value="__('Nama Perusahaan')" />
                    <x-text-input id="company_name" class="mt-2 block w-full rounded-2xl border-slate-300 px-4 py-3 text-sm shadow-sm focus:border-violet-500 focus:ring-violet-500" type="text" name="company_name" :value="old('company_name')" required />
                    <x-input-error :messages="$errors->get('company_name')" class="mt-2" />
                </div>

                <div>
                    <x-input-label for="industry" class="text-sm font-semibold text-slate-700" :value="__('Bidang Industri')" />
                    <x-text-input id="industry" class="mt-2 block w-full rounded-2xl border-slate-300 px-4 py-3 text-sm shadow-sm focus:border-violet-500 focus:ring-violet-500" type="text" name="industry" :value="old('industry')" />
                    <x-input-error :messages="$errors->get('industry')" class="mt-2" />
                </div>

                <div>
                    <x-input-label for="company_email" class="text-sm font-semibold text-slate-700" :value="__('Email Perusahaan')" />
                    <x-text-input id="company_email" class="mt-2 block w-full rounded-2xl border-slate-300 px-4 py-3 text-sm shadow-sm focus:border-violet-500 focus:ring-violet-500" type="email" name="company_email" :value="old('company_email')" />
                    <x-input-error :messages="$errors->get('company_email')" class="mt-2" />
                </div>

                <div>
                    <x-input-label for="contact_person" class="text-sm font-semibold text-slate-700" :value="__('Contact Person')" />
                    <x-text-input id="contact_person" class="mt-2 block w-full rounded-2xl border-slate-300 px-4 py-3 text-sm shadow-sm focus:border-violet-500 focus:ring-violet-500" type="text" name="contact_person" :value="old('contact_person')" />
                    <x-input-error :messages="$errors->get('contact_person')" class="mt-2" />
                </div>

                <div>
                    <x-input-label for="contact_person_phone" class="text-sm font-semibold text-slate-700" :value="__('Nomor Contact Person')" />
                    <x-text-input id="contact_person_phone" class="mt-2 block w-full rounded-2xl border-slate-300 px-4 py-3 text-sm shadow-sm focus:border-violet-500 focus:ring-violet-500" type="text" name="contact_person_phone" :value="old('contact_person_phone')" />
                    <x-input-error :messages="$errors->get('contact_person_phone')" class="mt-2" />
                </div>

                <div>
                    <x-input-label for="phone" class="text-sm font-semibold text-slate-700" :value="__('Nomor Telepon Perusahaan')" />
                    <x-text-input id="phone" class="mt-2 block w-full rounded-2xl border-slate-300 px-4 py-3 text-sm shadow-sm focus:border-violet-500 focus:ring-violet-500" type="text" name="phone" :value="old('phone')" />
                    <x-input-error :messages="$errors->get('phone')" class="mt-2" />
                </div>

                <div>
                    <x-input-label for="website" class="text-sm font-semibold text-slate-700" :value="__('Website')" />
                    <x-text-input id="website" class="mt-2 block w-full rounded-2xl border-slate-300 px-4 py-3 text-sm shadow-sm focus:border-violet-500 focus:ring-violet-500" type="url" name="website" :value="old('website')" placeholder="https://contoh.com" />
                    <x-input-error :messages="$errors->get('website')" class="mt-2" />
                </div>

                <div class="sm:col-span-2">
                    <x-input-label for="address" class="text-sm font-semibold text-slate-700" :value="__('Alamat Perusahaan')" />
                    <textarea id="address" name="address" rows="3" class="mt-2 block w-full rounded-2xl border-slate-300 px-4 py-3 text-sm shadow-sm focus:border-violet-500 focus:ring-violet-500" required>{{ old('address') }}</textarea>
                    <x-input-error :messages="$errors->get('address')" class="mt-2" />
                </div>

                <div class="sm:col-span-2">
                    <x-input-label for="description" class="text-sm font-semibold text-slate-700" :value="__('Deskripsi Singkat')" />
                    <textarea id="description" name="description" rows="3" class="mt-2 block w-full rounded-2xl border-slate-300 px-4 py-3 text-sm shadow-sm focus:border-violet-500 focus:ring-violet-500">{{ old('description') }}</textarea>
                    <x-input-error :messages="$errors->get('description')" class="mt-2" />
                </div>
            @endif
        </div>

        <div class="mt-8 flex items-center justify-between gap-4 border-t border-slate-200 pt-7 pb-1">
            <div>
                <!-- <p class="text-sm text-slate-600">
                    Langkah terakhir sebelum masuk ke dashboard {{ strtolower($meta['label']) }}.
                </p> -->
                <p class="mt-1 text-xs text-slate-500">
                    Jika akun Google ini tidak jadi dipakai, Anda bisa membatalkan pendaftaran dan kembali ke halaman daftar.
                </p>
            </div>

            <div class="flex items-center gap-3">
                <button type="submit"
                        form="cancel-google-registration"
                        class="inline-flex items-center justify-center rounded-2xl border border-slate-300 px-5 py-3 text-sm font-semibold text-slate-700 transition hover:border-slate-400 hover:bg-slate-50">
                    Batalkan Pendaftaran Google
                </button>
                <x-primary-button class="inline-flex min-w-[152px] items-center justify-center rounded-2xl bg-slate-950 px-6 py-3.5 text-sm font-semibold text-white transition hover:bg-slate-800 focus:bg-slate-800 active:bg-slate-900">
                    {{ __('Simpan Profil') }}
                </x-primary-button>
            </div>
        </div>
    </form>

    <form id="cancel-google-registration"
          method="POST"
          action="{{ route('register.complete.cancel') }}"
          onsubmit="return confirm('Batalkan pendaftaran Google ini? Akun sementara akan dihapus.');">
        @csrf
    </form>
</x-guest-layout>
