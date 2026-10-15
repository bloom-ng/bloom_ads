<x-guest-layout isAuth="true">
    <section class="flex flex-col w-full items-center bg-[url('/images/lines.png')] bg-cover bg-center mb-10 lg:mb-16">
        <div class="bg-gradient-to-r from-[#E6E6F366] to-[#6666B366] p-10 md:p-16 lg:p-20 lg:w-[40%] rounded-3xl items-center text-center mt-10 mx-5 md:mx-0 md:mt-16 lg:mt-24"
            x-data="{
                changing: {{ $pendingNumber && !$errors->has('phone_number') ? 'false' : 'true' }},
                resendIn: {{ $resendIn }},
                init() { setInterval(() => { if (this.resendIn > 0) this.resendIn-- }, 1000) }
            }">

            <h1 class="text-3xl md:text-4xl lg:text-5xl font-bold text-black mb-5">
                {{ $user->hasVerifiedPhone() ? 'Change Your Phone Number' : 'Verify Your Phone Number' }}
            </h1>

            {{-- Step A: confirm or edit the number --}}
            <div x-show="changing" @if ($pendingNumber && !$errors->has('phone_number')) style="display: none" @endif>
                <p class="text-xl mb-8">
                    @if ($user->hasVerifiedPhone())
                        Enter your new number and we'll text you a 6-digit code to confirm it.
                    @else
                        To keep your account secure, confirm your mobile number. We'll text you a 6-digit code.
                    @endif
                </p>

                <form method="POST" action="{{ route('phone.send') }}" class="flex flex-col space-y-4">
                    @csrf
                    <div class="flex flex-row gap-3">
                        <div class="relative w-28 shrink-0">
                            <label for="country_code" class="sr-only">Country Code</label>
                            <select id="country_code" name="country_code"
                                class="w-full pr-10 px-3 py-3 border border-[#000000] rounded-xl bg-transparent text-black">
                                @foreach ($countryCodes as $countryCode)
                                    <option value="{{ $countryCode['code'] }}"
                                        @selected(old('country_code', $pending['country_code'] ?? $user->phone_country_code) === $countryCode['code'])>
                                        {{ $countryCode['code'] }} ({{ $countryCode['name'] }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="w-full">
                            <label for="phone_number" class="sr-only">Phone Number</label>
                            <input type="tel" id="phone_number" name="phone_number" autocomplete="tel-national"
                                value="{{ old('phone_number', $pending['phone'] ?? ($user->hasVerifiedPhone() ? '' : $user->phone)) }}"
                                class="w-full py-3 px-5 border border-[#000000] rounded-xl bg-transparent text-black"
                                placeholder="Phone Number" required>
                        </div>
                    </div>
                    @error('phone_number')
                        <p class="text-red-600 text-left">{{ $message }}</p>
                    @enderror

                    <button type="submit" class="w-full bg-[#000080] text-white px-5 py-3 rounded-xl">
                        Send Code
                    </button>

                    @if ($pendingNumber)
                        <button type="button" @click="changing = false" class="text-[#000080] underline">
                            I already have a code
                        </button>
                    @endif
                </form>
            </div>

            {{-- Step B: enter the code --}}
            @if ($pendingNumber)
                <div x-show="!changing" @if ($errors->has('phone_number')) style="display: none" @endif>
                    <p class="text-xl mb-8">Enter the 6-digit code we sent to <strong>{{ $pendingNumber }}</strong>.</p>

                    <form method="POST" action="{{ route('phone.verify') }}" class="flex flex-col space-y-4">
                        @csrf
                        <label for="code" class="sr-only">Verification Code</label>
                        <input type="text" id="code" name="code" inputmode="numeric" autocomplete="one-time-code"
                            pattern="[0-9]{6}" maxlength="6" required autofocus
                            class="w-full py-3 px-5 border border-[#000000] rounded-xl bg-transparent text-black text-center text-2xl tracking-[0.5em]"
                            placeholder="000000">
                        @error('code')
                            <p class="text-red-600 text-left">{{ $message }}</p>
                        @enderror

                        <button type="submit" class="w-full bg-[#000080] text-white px-5 py-3 rounded-xl">
                            Verify
                        </button>
                    </form>

                    <div class="flex flex-row justify-between mt-4">
                        <form method="POST" action="{{ route('phone.send') }}">
                            @csrf
                            <input type="hidden" name="country_code" value="{{ $pending['country_code'] }}">
                            <input type="hidden" name="phone_number" value="{{ $pending['phone'] }}">
                            <button type="submit" class="text-[#000080] underline disabled:no-underline disabled:text-gray-500"
                                :disabled="resendIn > 0">
                                <span x-show="resendIn > 0">Resend code in <span x-text="resendIn"></span>s</span>
                                <span x-show="resendIn <= 0">Resend code</span>
                            </button>
                        </form>

                        <button type="button" @click="changing = true" class="text-[#000080] underline">
                            Use a different number
                        </button>
                    </div>
                </div>
            @endif

            <p class="mt-8 text-sm">
                Not receiving the code?
                <a href="https://wa.me/+2347086278644" target="_blank" class="text-[#000080] underline">Contact support on WhatsApp</a>
            </p>

            @if ($user->hasVerifiedPhone())
                <a href="{{ route('account') }}"
                    class="block mt-4 w-full bg-gray-200 text-gray-700 px-5 py-3 rounded-xl hover:bg-gray-300">
                    Back to Account
                </a>
            @else
                <form method="POST" action="{{ route('user.logout') }}" class="mt-4">
                    @csrf
                    <button type="submit" class="w-full bg-gray-200 text-gray-700 px-5 py-3 rounded-xl hover:bg-gray-300">
                        Log Out
                    </button>
                </form>
            @endif
        </div>
    </section>
</x-guest-layout>
