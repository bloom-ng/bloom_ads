<x-guest-layout>
    <section class="flex flex-col w-full items-center bg-[url('/images/lines.png')] bg-cover bg-center mb-10 lg:mb-16">
        <div
            class="bg-gradient-to-r from-[#E6E6F366] to-[#6666B366] p-14 md:p-16 lg:p-20 lg:w-[40%] rounded-3xl items-center text-center mt-24">
            <h1 class="text-5xl font-bold text-black mb-5">Two-Factor Authentication</h1>

        <form action="{{ route('2fa.verify') }}" method="POST"> <!-- Corrected route name -->
            @csrf
            <div class="form-group pb-2">
                <label class="text-xl font-light mb-10" for="code">Enter the code sent to your email:</label>
                <input type="text" name="code" id="code" class="w-full py-1.5 px-2.5 border border-[#000000] rounded-xl bg-transparent form-control" required>
            </div>
            <button type="submit" class="w-full py-3 bg-[#000080] font-semibold text-white rounded-xl btn btn-primary">Verify</button>
        </form>
        </div>
    </section>
</x-guest-layout>
