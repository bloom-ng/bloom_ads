<x-guest-layout :isAuth="true">
    <section class="flex flex-col w-full items-center bg-[url('/images/lines.png')] bg-cover bg-center lg:mb-16">
        <div
            class="bg-gradient-to-r from-[#FFE5C680] to-[#FFBB6780] mt-10 mb-10 mx-5 p-14 md:p-16 lg:p-20 rounded-3xl items-center text-center ">
            <h1 class="text-3xl md:text-4xl lg:text-5xl font-bold text-black mb-14">How do you plan <br>to collaborate
                with us?</h1>

            <div class="flex flex-col items-center gap-y-5">
                <a href="/signup1" class="text-center w-full p-10 border border-[#000000] rounded-3xl">
                    <p class="text-center text-xl font-semibold">Direct Advertiser</p>
                    <p class="text-center text-xs font-semibold">(I and my representative manages my advertising)</p>
                </a>

                <a href="/signup2" class="text-center w-full p-10  border border-[#000000] rounded-3xl">
                    <p class="text-center text-xl font-semibold">Agency</p>
                    <p class="text-center text-xs font-semibold">(I manage advertising on behalf of my clients)</p>
                </a>

                <a href="/signup3" class="text-center w-full p-10 border border-[#000000] rounded-3xl">
                    <p class="text-center text-xl font-semibold">Partner</p>
                    <p class="text-center text-xs font-semibold">(I have a monthly ad budget of over 50,000 USD)</p>
                </a>

                <p class="font-semibold mt-5">Already Collaborating With Us? <a href="/login"><span
                            class="text-[#FF8C00]">Login</span> </a></p>

            </div>
        </div>
    </section>
</x-guest-layout>
