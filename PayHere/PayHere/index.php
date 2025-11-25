<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PayHere | Demo</title>

    <!-- CSS Styles -->
    <link rel="stylesheet" href="style.css">
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/flowbite/2.2.0/flowbite.min.css" rel="stylesheet" />

</head>
<body>
    <main class="relative bg-gray-300 w-full h-full min-h-screen">
        <div class="bg-white rounded-xl shadow-lg p-10 absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2">
            <div class="flex flex-col justify-center gap-10 place-content-center align-center items-center">
                <div class="items-center align-center mx-auto">
                    <button type="button" name="payBtn" onClick="paymentGateway()" class="text-black hover:text-orange-300 bg-white hover:bg-blue-600 border border-gray-200 focus:ring-4 focus:outline-none focus:ring-gray-100 font-medium rounded-lg text-sm px-5 py-2.5 text-center inline-flex items-center me-2 mb-2 mx-auto">
                        Pay with PayHere
                    </button>
                </div>
                <div>
                    <a href="https://www.payhere.lk" target="_blank"><img src="https://www.payhere.lk/downloads/images/payhere_short_banner_dark.png" alt="PayHere" width="250"/></a>
                </div>
            </div>
        </div>
    </main>

    <!-- Tailwind Script -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/flowbite/2.2.0/flowbite.min.js"></script>

    <!-- PayHere Script -->
    <script type="text/javascript" src="https://www.payhere.lk/lib/payhere.js"></script>

    <!-- Payment Process Script -->
    <script src="paymentGateway.js"></script>

</body>
</html>