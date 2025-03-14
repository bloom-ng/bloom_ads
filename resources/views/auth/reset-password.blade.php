<x-guest-layout isAuth="true">
    <section class="flex flex-col w-full items-center bg-[url('/images/lines.png')] bg-cover bg-center mb-10 lg:mb-16">
        <div
            class="bg-gradient-to-r from-[#FFE5C680] to-[#FFBB6780] p-10 md:p-16 lg:p-20 rounded-3xl items-center text-center mx-5 mt-5 md:mt-10">
            <h1 class="text-3xl md:text-4xl lg:text-5xl font-bold text-black mb-5">Reset Password</h1>
            <p class="text-2xl font-light mb-10">Please enter your new password.</p>

            <form class="space-y-6 w-full lg:px-10" method="POST" action="{{ route('password.update') }}">
                @csrf
                <input type="hidden" name="token" value="{{ $request->route('token') }}">
                <input type="hidden" name="email" value="{{ $request->email }}">

                <div class="mb-3">
                    <label for="password"></label>
                    <input type="password" id="password" name="password"
                        class="w-full py-3 px-5 border border-[#000000] rounded-xl bg-transparent @error('password') border-red-500 @enderror"
                        placeholder="New Password" required>
                    @error('password')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="password_confirmation"></label>
                    <input type="password" id="password_confirmation" name="password_confirmation"
                        class="w-full py-3 px-5 border border-[#000000] rounded-xl bg-transparent"
                        placeholder="Confirm Password" required>
                </div>

                <button type="submit"
                    class="w-full py-3 bg-[#FF8C00] font-semibold text-black rounded-xl hover:bg-[#e67e00]">
                    Reset Password
                </button>
            </form>
        </div>
    </section>
</x-guest-layout>
