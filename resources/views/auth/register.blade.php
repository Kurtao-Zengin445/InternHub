<x-guest-layout>
    @php
        $meta = $roleMeta[$selectedRole];
    @endphp

    <div class="mb-8">
        <div class="inline-flex items-center gap-2 rounded-full bg-amber-50 px-3 py-1 text-xs font-semibold uppercase tracking-[0.2em] text-amber-700">
            <span class="h-2 w-2 rounded-full bg-amber-500"></span>
            Register
        </div>
        <h1 class="mt-4 text-3xl font-bold tracking-tight text-slate-950" style="font-family:'Syne',sans-serif;">
            Daftar Akun {{ $meta['label'] }}
        </h1>
        <p class="mt-2 text-sm text-slate-600">
            {{ $meta['description'] }}
        </p>
    </div>

    <div class="mb-6">
        <div class="mb-3 text-sm font-semibold text-slate-700">Pilih jenis akun</div>
        <div class="grid gap-3 sm:grid-cols-2">
            @foreach ($roleMeta as $role => $item)
                <a href="{{ route('register', ['role' => $role]) }}"
                   class="rounded-2xl border px-4 py-4 transition {{ $selectedRole === $role ? 'border-slate-900 bg-slate-900 text-white shadow-lg shadow-slate-900/10' : 'border-slate-200 bg-white text-slate-700 hover:border-slate-300 hover:bg-slate-50' }}">
                    <div class="flex items-start gap-3">
                        <div class="flex h-11 w-11 items-center justify-center rounded-xl {{ $selectedRole === $role ? 'bg-white/10 text-white' : 'bg-slate-100 text-slate-700' }}">
                            <i class="bi {{ $item['icon'] }} text-lg"></i>
                        </div>
                        <div>
                            <div class="text-sm font-semibold">{{ $item['label'] }}</div>
                            <p class="mt-1 text-xs leading-5 {{ $selectedRole === $role ? 'text-slate-200' : 'text-slate-500' }}">
                                {{ $item['description'] }}
                            </p>
                        </div>
                    </div>
                </a>
            @endforeach
        </div>
    </div>

    <div class="space-y-3">
        <a href="{{ route('google.redirect', ['role' => $selectedRole]) }}"
               class="inline-flex w-full items-center justify-center gap-3 rounded-2xl border border-slate-300 bg-white px-4 py-3 text-sm font-semibold text-slate-700 shadow-sm transition hover:bg-slate-50 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 48 48" class="h-5 w-5" aria-hidden="true">
                    <path fill="#FFC107" d="M43.6 20.5H42V20H24v8h11.3C33.6 32.7 29.2 36 24 36c-6.6 0-12-5.4-12-12S17.4 12 24 12c3 0 5.8 1.1 7.9 3l5.7-5.7C34.1 6.1 29.3 4 24 4 12.9 4 4 12.9 4 24s8.9 20 20 20 20-8.9 20-20c0-1.3-.1-2.4-.4-3.5Z"/>
                    <path fill="#FF3D00" d="M6.3 14.7l6.6 4.8C14.7 15 18.9 12 24 12c3 0 5.8 1.1 7.9 3l5.7-5.7C34.1 6.1 29.3 4 24 4c-7.7 0-14.3 4.3-17.7 10.7Z"/>
                    <path fill="#4CAF50" d="M24 44c5.2 0 10-2 13.5-5.2l-6.2-5.2c-2 1.5-4.5 2.4-7.3 2.4-5.2 0-9.6-3.3-11.2-8l-6.5 5C9.6 39.5 16.3 44 24 44Z"/>
                    <path fill="#1976D2" d="M43.6 20.5H42V20H24v8h11.3c-.8 2.3-2.2 4.2-4 5.6l6.2 5.2C36.9 38.5 44 33 44 24c0-1.3-.1-2.4-.4-3.5Z"/>
                </svg>
                <span>{{ __('Daftar dengan Google sebagai ') . $meta['label'] }}</span>
            </a>

        <div class="relative">
            <div class="absolute inset-0 flex items-center">
                <div class="w-full border-t border-slate-200"></div>
            </div>
            <div class="relative flex justify-center text-xs uppercase">
                <span class="bg-white px-3 text-slate-400">{{ __('Atau isi data manual') }}</span>
            </div>
        </div>
    </div>

    <form method="POST" action="{{ route('register') }}" class="mt-6">
        @csrf
        <input type="hidden" name="role" value="{{ $selectedRole }}">

        <div class="grid gap-5 sm:grid-cols-2">
            @if ($selectedRole === 'student')
                <div class="sm:col-span-2">
                    <x-input-label for="name" class="text-sm font-semibold text-slate-700" :value="__('Nama Lengkap')" />
                    <x-text-input id="name" class="mt-2 block w-full rounded-2xl border-slate-300 px-4 py-3 text-sm shadow-sm focus:border-blue-500 focus:ring-blue-500" type="text" name="name" :value="old('name')" required autofocus autocomplete="name" />
                    <x-input-error :messages="$errors->get('name')" class="mt-2" />
                </div>

                <div class="sm:col-span-2">
                    <x-input-label for="email" class="text-sm font-semibold text-slate-700" :value="__('Email')" />
                    <x-text-input id="email" class="mt-2 block w-full rounded-2xl border-slate-300 px-4 py-3 text-sm shadow-sm focus:border-blue-500 focus:ring-blue-500" type="email" name="email" :value="old('email')" required autocomplete="username" />
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
                    <x-text-input id="name" class="mt-2 block w-full rounded-2xl border-slate-300 px-4 py-3 text-sm shadow-sm focus:border-emerald-500 focus:ring-emerald-500" type="text" name="name" :value="old('name')" required autofocus autocomplete="name" />
                    <x-input-error :messages="$errors->get('name')" class="mt-2" />
                </div>

                <div class="sm:col-span-2">
                    <x-input-label for="email" class="text-sm font-semibold text-slate-700" :value="__('Email Login')" />
                    <x-text-input id="email" class="mt-2 block w-full rounded-2xl border-slate-300 px-4 py-3 text-sm shadow-sm focus:border-emerald-500 focus:ring-emerald-500" type="email" name="email" :value="old('email')" required autocomplete="username" />
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
                    <x-text-input id="name" class="mt-2 block w-full rounded-2xl border-slate-300 px-4 py-3 text-sm shadow-sm focus:border-amber-500 focus:ring-amber-500" type="text" name="name" :value="old('name')" required autofocus autocomplete="name" />
                    <x-input-error :messages="$errors->get('name')" class="mt-2" />
                </div>

                <div class="sm:col-span-2">
                    <x-input-label for="email" class="text-sm font-semibold text-slate-700" :value="__('Email Login')" />
                    <x-text-input id="email" class="mt-2 block w-full rounded-2xl border-slate-300 px-4 py-3 text-sm shadow-sm focus:border-amber-500 focus:ring-amber-500" type="email" name="email" :value="old('email')" required autocomplete="username" />
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
                    <x-text-input id="name" class="mt-2 block w-full rounded-2xl border-slate-300 px-4 py-3 text-sm shadow-sm focus:border-violet-500 focus:ring-violet-500" type="text" name="name" :value="old('name')" required autofocus autocomplete="name" />
                    <x-input-error :messages="$errors->get('name')" class="mt-2" />
                </div>

                <div class="sm:col-span-2">
                    <x-input-label for="email" class="text-sm font-semibold text-slate-700" :value="__('Email Login')" />
                    <x-text-input id="email" class="mt-2 block w-full rounded-2xl border-slate-300 px-4 py-3 text-sm shadow-sm focus:border-violet-500 focus:ring-violet-500" type="email" name="email" :value="old('email')" required autocomplete="username" />
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

            <div>
                <x-input-label for="password" class="text-sm font-semibold text-slate-700" :value="__('Password')" />
                <x-text-input id="password" class="mt-2 block w-full rounded-2xl border-slate-300 px-4 py-3 text-sm shadow-sm focus:border-slate-500 focus:ring-slate-500" type="password" name="password" required autocomplete="new-password" />
                <x-input-error :messages="$errors->get('password')" class="mt-2" />
            </div>

            <div>
                <x-input-label for="password_confirmation" class="text-sm font-semibold text-slate-700" :value="__('Konfirmasi Password')" />
                <x-text-input id="password_confirmation" class="mt-2 block w-full rounded-2xl border-slate-300 px-4 py-3 text-sm shadow-sm focus:border-slate-500 focus:ring-slate-500" type="password" name="password_confirmation" required autocomplete="new-password" />
                <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
            </div>
        </div>

        <div class="mt-8 flex items-center justify-between gap-4 border-t border-slate-200 pt-7 pb-1">
            <a class="text-sm text-slate-600 transition hover:text-slate-900" href="{{ route('login') }}">
                {{ __('Sudah punya akun?') }}
            </a>

            <x-primary-button class="inline-flex min-w-[132px] items-center justify-center rounded-2xl bg-slate-950 px-6 py-3.5 text-sm font-semibold text-white transition hover:bg-slate-800 focus:bg-slate-800 active:bg-slate-900">
                {{ __('Daftar') }}
            </x-primary-button>
        </div>
    </form>
</x-guest-layout>
