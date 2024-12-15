<x-guest-layout isAuth="true">
    <section class="flex flex-col w-full items-center bg-[url('/images/lines.png')] bg-cover bg-center mb-10 lg:mb-16">
        <div
            class="bg-gradient-to-r from-[#FFE5C680] to-[#FFBB6780] p-14 md:p-16 lg:p-20 lg:w-[40%] rounded-3xl items-center text-center mt-24">
            <h1 class="text-5xl font-bold text-black mb-5">Login</h1>
            <p class="text-2xl font-light mb-10">Welcome Back!</p>


            <form class="space-y-6 w-full lg:px-5" action="{{ route('admin.authenticate') }}" method="POST">
                @csrf
                <div class="mb-3">
                    <label for="email"></label>
                    <input type="text" id="email" name="email"
                        class="w-full py-3 px-5 border border-[#000000] rounded-xl bg-transparent" placeholder="Email"
                        required>
                </div>

                <div class="mb-3">
                    <label for="password"></label>
                    <input type="password" id="password" name="password"
                        class="w-full py-3 px-5 border border-[#000000] rounded-xl bg-transparent"
                        placeholder="Password" required>
                </div>

                <div class="w-full flex flex-row justify-end">
                    <a href="/forgot" class="font-semibold text-base">Forgot your password?</a>
                </div>

                <button type="submit"
                    class="w-full py-3 bg-[#FF8C00] font-semibold text-black rounded-xl">Login</button>

                <!-- <p class="font-semibold">Not Registered? <a href="/signup"><span class="text-[#FF8C00]">Sign Up
                            Now</span></a></p> -->
            </form>
        </div>
    </section>
</x-guest-layout>
