<x-guest-layout isAuth="true">
    <section class="flex flex-col w-full items-center bg-[url('/images/lines.png')] bg-cover bg-center mb-10 lg:mb-16">
        <div
            class="bg-gradient-to-r from-[#E6E6F366] to-[#6666B366] p-10 md:p-16 lg:p-20 rounded-3xl items-center text-center mx-5 mt-5 md:mt-10">
            <h1 class="text-3xl md:text-4xl lg:text-5xl font-bold text-black mb-5">Forgot Password?</h1>
            <p class="text-2xl font-light mb-10">Enter your email and let’s help you.</p>
            <form class="space-y-6 w-full lg:px-10">
                <div class="mb-3">
                    <label for="email"></label>
                    <input type="text" id="email" name="email"
                        class="w-full py-3 px-5 border border-[#000000] rounded-xl bg-transparent" placeholder="Email"
                        required>
                </div>

                <button type="submit" class="w-full py-3 bg-[#000080] font-semibold rounded-xl text-white">Send Reset
                    Link</button>
            </form>
        </div>
    </section>
</x-guest-layout>
