<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bloom Ads | Reset Password</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body>
    <section class="bg-white">
        <!-- desktop header -->
        <header class="md:flex flex-row w-full justify-between items-center bg-[#000000] py-8 px-16 hidden">
            <div class="w-1/5">
                <img src="/images/Bloomlogo.png" alt="" class="w-[107px] h-[35px]">
            </div>
        </header>

        <!-- mobile header -->
        <header class="flex flex-row w-full bg-[#000000] justify-between items-center px-8 py-5 md:hidden">
            <a href="#"><img src="/images/Bloomlogo.png" alt="" class="w-[107px] h-[35px]"></a>

            <!-- <button id="menu-btn" class="text-gray-700 focus:outline-none">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="white" xmlns="http://www.w3.org/2000/svg">
                    <path d="M3 12h18M3 6h18M3 18h18" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>                  
            </button> -->

            <a href="#" class="rounded-xl bg-[#FF8C00] text-black font-semibold text-base p-2 px-7">Get Started</a>
        </header>

        <section class="flex flex-col w-full items-center bg-[url('/images/lines.png')] bg-cover bg-center mb-10 lg:mb-16">
            <div class="bg-gradient-to-r from-[#FFE5C680] to-[#FFBB6780] p-10 md:p-16 lg:p-20 rounded-3xl items-center text-center mx-5 mt-5 md:mt-10">
                <h1 class="text-3xl md:text-4xl lg:text-5xl font-bold text-black mb-5">Forgot Password?</h1>
                <p class="text-2xl font-light mb-10">Enter your email and let’s help you.</p>
               <form class="space-y-6 w-full lg:px-10">
                    <div class="mb-3">
                        <label for="email"></label>
                        <input type="text" id="email" name="email" class="w-full p-2 px-5 border border-[#000000] rounded-xl" 
                        placeholder="Email" required>
                    </div>

                    <button type="submit" class="w-full py-2 bg-[#FF8C00] font-semibold text-black rounded-xl">Send Reset Link</button>
               </form> 
            </div>
        </section>
        
        
        <footer class="bg-black flex flex-col w-full md:justify-center md:items-center md:py-14 md:px-0 px-10">
            <div class="flex flex-col md:flex-row md:space-x-5 lg:space-x-10 mt-5 lg:mt-0 mb-10 text-white text-[14px] md:text-xl lg:text-3xl">
                <a href="">Service Agreement</a>
                <a href="">Purchase Policy</a>
                <a href="">Privacy Policy</a>
                <a href="">Contact Us</a>
            </div>

            <div class="flex flex-row lg:space-x-3 mb-10">
                <a href=""><img src="/images/Instagram.png" alt="instagram logo"></a>
                <a href=""><img src="/images/Facebook.png" alt="facebook logo"></a>
                <a href=""><img src="/images/LinkedIn.png" alt="linkedin logo"></a>
                <a href=""><img src="/images/TwitterX.png" alt="twitter logo"></a>
            </div>

            <div class="flex flex-row mb-5 space-x-2">
                <svg width="25" height="25" viewBox="0 0 25 25" fill="none" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" class="mt-1">
                    <rect width="25" height="25" fill="url(#pattern0_561_390)"/>
                    <defs>
                    <pattern id="pattern0_561_390" patternContentUnits="objectBoundingBox" width="1" height="1">
                    <use xlink:href="#image0_561_390" transform="scale(0.0111111)"/>
                    </pattern>
                    <image id="image0_561_390" width="90" height="90" xlink:href="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAFoAAABaCAYAAAA4qEECAAAACXBIWXMAAAsTAAALEwEAmpwYAAAFcElEQVR4nO2da4jUVRTAr2VZWFaSlCSUWJShiVYfpIcSPSCKXmTgFqHVkihtZu+nINGDCNkIAqkPBfUhen9QE5QiLBZjfUIQayWZ2bKbPd0l7ReHPZINzcz9z5z7f94fzJdh5pxzz8zcxznnnnEuEolEIpFIJBKJRCJ+AEcCFwJ3AM8D7wNbgT5gEBjWx6A+t1Vf85y+5wKR4amuWgCTgHuBj4BfaJ99wIdAF3CaqzLAscBtwDrgIOE4AHwM3Aoc46oCcJx+03aTPj8By4ETXFkBjgIeAAbIHrFhmdjkygRwCbCN/PEVcLkrOjInAi8Df5NfxLZuYIwrIsAZwBcUhy+BM12RkJ+j0TYtbWRbeJkrAsANwH6KyzBwi8szQGfgPXFayBjucnkEuF4PB2XhIDDP5QmZ14AhyscwcKXLA8BZBV34kiyQU7J28hjdFpWdTZnus/UwUhW6szxW5/nEZ42MdU4WAaLtpMNO4A3gUaADuFoPRDcCi4AnNPAvc2loJMkwOk1HSxQudDhzBTA1YWZmNvCa7hZCsTSsd/8d0PEBQ52/Aw9LUsAgYxMqmNUvMXU7j9YfxIOEodd6GwVcBewJYOsySzvrpZ9+DGD4mna/xQ1sPgXYYWzvD0HTYprjs+bT0Lk8SdAC3xjb3RHSYEmkWvI9MCGYwf+1/RzjqOKaUIZOChCZu7bFqWCGJhYS1W8ATxraLgG0iUnt9zFS6i4sWZtA9zjgaWBXjQzZO78OnJsgZCBFN1YsacupdYyU4hZLZnvqPc9jfpXIYaenvEcMx/Be246tMW608clrS4Lpyrf+Q/bM8z1knm64vx40LT/TWjhLHvLU+05CuXKQGu8ht8dwLOebOFkNk+JBS6YH/HCbHpGBVwzHssDS0VLVacXPwCgPnS+FWmSN5+lnLB39gaFhGzx1ftei/D91v9/oIRVK+VsQZfEyNGyVh76TKQ69lo62PL4+5aFvDsVhp6WjLcOi93jok6B+Uei3dLRlIL3TQ9/tFIeh6OgCOtpy6ujyrN+r5NRhuRgu98ywV3IxtNzeveqh7yQqur2TdL4Vn3jqlDKDVuPEfU0ekmW34t28HsF/A47w0Plii/J7PGRLpj2XR3DroNJMD50zWwxnrvCQvSqvQSXrMOljnnrfSij3D4lhe8jdYDiWWSZOPqwKyDLwv8NT78SEwaVFHjJPNKzltg38q4Fyt9qSSz31TgW+biLrL+D+DKZBu4XwMAPlWrEl6xPoHqtXi7+tkfGrTi8zPOWMAjYajmFxW05tUIRifUflphbsmKDlBpOTVnYaFwDJr+jUpPb7GipdAizZE6Q24v9tHw/sNbR9dUhjpRWDNRtD1d3VTD2fGds9P3SRY4jqzPWhSmH1PvpaY3t3B+/9EbAIfRtwtrGt04zjNIe4z9LOLArR90uqq91vtzQ+kUNRoKvSUog+1s6jjQciTUVCMiAxBJ/6j5qt23TghcD3HpvG060vC6XV6GQX8CbwuC7G1+hlIUkO3K3Pv20ckavH5lQvC6mzL47X31JCO7dUhZVp+bVevbFc3y07PcDRmTlanT0lpcuUWSG1gpNdHgDmFrzrTD2knuUKlyeA60rYGOVml0ekPU5JWv0cAO50eUZb/hR5GhnKXYufJq1/9hV04ZvrioReyvmc4rAp85Y+be6zu3N+ghTbVma+TzY8rm8hf4hNF7kywchdxaUaZsyafk02pxsgShNG0kpdeuE+bfZqNn2cqwqMpJg6tFdHyIOOyF4tOb5KtZ5vUJG0RK6SafVPuwyqrMXBSgKKDiPlZ7OAhcCzUg2kbYD6NPNy6O9BBvS5Xn2NvHaBvjf+PUgkEolEIpFIJBKJOE/+AVhJq2eDC+U0AAAAAElFTkSuQmCC"/>
                    </defs>
                </svg>
                <p class="text-white text-xl">Copyright 2024</p>
            </div>
        </footer>
    </section>
</body> 
</html>