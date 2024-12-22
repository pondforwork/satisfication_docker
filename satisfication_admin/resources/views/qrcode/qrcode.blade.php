<x-app-layout>
    <!DOCTYPE html>
    <html>

    <head>
        <title>QR Code</title>
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <style>
            /* Hide everything else during printing */
            @media print {
                @page {
                    size: auto;
                    margin: 0;
                }

                body * {
                    visibility: hidden;
                }

                #printableArea,
                #printableArea * {
                    visibility: visible;
                }

                #printableArea {
                    position: absolute;
                    left: 50%;
                    top: 50%;
                    transform: translate(-50%, -50%);
                    text-align: center;
                }
            }

            #printableArea {
                display: flex;
                flex-direction: column;
                justify-content: center;
                align-items: center;
                text-align: center;
                height: 50vh;
            }

            #printableArea h3 {
                margin-bottom: 20px;
            }
        </style>
    </head>

    <body>
        <div id="printableArea">
            <div>{!! $qrCode !!}</div>
            <h2 style="padding-top: 20px;">{{ $counterName }}</h2>
        </div>
        <button id="printButton">Print to PDF</button>

        <script>
            document.getElementById('printButton').addEventListener('click', function() {
                window.print();
            });
        </script>
    </body>

    </html>
</x-app-layout>
