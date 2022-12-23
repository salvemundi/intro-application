@extends('layouts.app')
@section('content')

<script>
    setActive("qrcode");
</script>
<div class="row mt-4 mt-sm-1">
    <div>
        <main class="wrapper">
            <section class="center d-flex mt-2 text-center" id="demo-content">
                <div>
                    <h1 class="title">Scan QR Code om in / uit the checken!</h1>
                    <div class="btn-group mb-2">
                        <input type="radio" class="btn-check" name="options" id="checkIn" autocomplete="off" checked />
                        <label class="btn btn-primary" for="checkIn">Inchecken</label>

                        <input type="radio" class="btn-check" name="options" id="checkOut" autocomplete="off" />
                        <label class="btn btn-primary" for="checkOut">Uitchecken</label>
                    </div>
                    <div class="mb-2">
                        <a class="btn btn-primary" id="startButton">Start</a>
                        <a class="btn btn-primary" id="resetButton">Reset</a>
                    </div>
                    <div class=" form-switch my-1 justify-content-center" style="transform: scale(1.5)">
                        <input class="form-check-input"  type="checkbox" role="switch" id="torchCheckbox">
                        <label class="form-check-label"  for="flexSwitchCheckDefault">Zaklamp</label>
                    </div>
                    <div id="sourceSelectPanel" style="display:none" class="center">
                        <select id="sourceSelect" class="form-select form-select-sm" style="max-width:400px">

                        </select>
                    </div>
                    <div>
                        <video id="video" width="300" height="200" style="border: 1px solid gray"></video>
                    </div>
                </div>
            </section>
        </main>
    </div>
    <div>
        <div class="card-body mx-auto participantCard text-left" style="max-width: 400px" id="particpant-card">
            <p id="allowed">Toegestaan: </p>
            <p id="name">Naam: </p>
            <p id="age">Leeftijd: </p>

        </div>
    </div>
</div>
<script type="text/javascript">
    function enableTorch(codeReader, value) {
        codeReader.stream.getVideoTracks()[0].applyConstraints({
            advanced: [{torch: value}] // or false to turn off the torch
        });
    }

    function decodeOnce(codeReader, selectedDeviceId) {
        codeReader.decodeFromInputVideoDevice(selectedDeviceId, 'video').then((result) => {
            console.log(result)
            document.getElementById('result').textContent = result.text
        }).catch((err) => {
            console.error(err)
            document.getElementById('result').textContent = err
        })
    }
    function setInformation(user, allowed) {
        let nameElement = document.getElementById('name');
        let ageElement = document.getElementById('age');
        let allowElement = document.getElementById('allowed')
        if(user.insertion) {
            nameElement.textContent = "Naam " + user.firstName + " " +user.insertion + " " + user.lastName
        } else  {
            nameElement.textContent = "Naam: " + user.firstName + " " + user.lastName
        }
        allowElement.textContent = "Toegestaan: " + allowed;
        ageElement.textContent = "Leeftijd: " + user.age;
    }
    function delay(time) {
        return new Promise(resolve => setTimeout(resolve, time));
    }

    async function flashBackgroundRed() {
        document.body.style.backgroundColor = "red";
        await delay(500);
        document.body.style.backgroundColor = "white";
    }

    async function flashBackgroundGreen() {
        document.body.style.backgroundColor = "green";
        await delay(250);
        document.body.style.backgroundColor = "white";
    }

    function decodeContinuously(codeReader, selectedDeviceId) {
        codeReader.decodeFromInputVideoDeviceContinuously(selectedDeviceId, 'video', (result, err) => {
            if (result) {
                // properly decoded qr code
                let check = document.getElementById('checkIn')
                console.log('Found QR code!', result)
                if(check.checked) {
                    $.ajax({
                        url: '/participants/' + result.text + "/get",
                        type: 'GET',
                        success: function(response) {
                            obj = JSON.parse(response);

                            if(obj.removedFromIntro){
                                setInformation(obj, "nee, permanent verwijderd");
                                document.getElementById('particpant-card').classList.remove('aboveEightTeenQR');
                                document.getElementById('particpant-card').classList.add('underEightTeenQR');
                                flashBackgroundRed();
                                return;
                            }
                            if(!obj.haspaid) {
                                setInformation(obj, "nee, niet betaald");
                                document.getElementById('particpant-card').classList.remove('aboveEightTeenQR');
                                document.getElementById('particpant-card').classList.add('underEightTeenQR');
                                flashBackgroundRed();
                                return;
                            }
                            setInformation(obj, "ja");
                            if(obj.above18){
                                document.getElementById('particpant-card').classList.remove('underEightTeenQR');
                                document.getElementById('particpant-card').classList.add('aboveEightTeenQR');
                            } else {
                                document.getElementById('particpant-card').classList.remove('aboveEightTeenQR');
                                document.getElementById('particpant-card').classList.add('underEightTeenQR');
                            }
                            $.ajax({
                                url: '/participants/' + result.text + "/checkIn",
                                type: 'POST',
                                success: function(response) {
                                    flashBackgroundGreen();
                                },
                                beforeSend: function (request) {
                                    return request.setRequestHeader('X-CSRF-Token', $("meta[name='csrf-token']").attr('content'));
                                }
                            });
                        },
                        beforeSend: function (request) {
                            return request.setRequestHeader('X-CSRF-Token', $("meta[name='csrf-token']").attr('content'));
                        }
                    });
                } else {
                    $.ajax({
                        url: '/participants/' + result.text + "/checkOut",
                        type: 'POST',
                        success: function(response) {
                            $.ajax({
                                url: '/participants/' + result.text + "/get",
                                type: 'GET',
                                success: function(response) {
                                    obj = JSON.parse(response)
                                    setInformation(obj,"N/A");
                                    flashBackgroundGreen();
                                },
                                beforeSend: function (request) {
                                    return request.setRequestHeader('X-CSRF-Token', $("meta[name='csrf-token']").attr('content'));
                                }
                            });
                        },
                        beforeSend: function (request) {
                            return request.setRequestHeader('X-CSRF-Token', $("meta[name='csrf-token']").attr('content'));
                        }
                    });
                }



            }

            if (err) {
                // As long as this error belongs into one of the following categories
                // the code reader is going to continue as excepted. Any other error
                // will stop the decoding loop.
                //
                // Excepted Exceptions:
                //
                //  - NotFoundException
                //  - ChecksumException
                //  - FormatException

                if (err instanceof ZXing.NotFoundException) {
                    console.log('No QR code found.')
                }

                if (err instanceof ZXing.ChecksumException) {
                    console.log('A code was found, but it\'s read value was not valid.')

                }

                if (err instanceof ZXing.FormatException) {
                    console.log('A code was found, but it was in a invalid format.')

                }
            }
        })
    }

    window.addEventListener('load', function () {
        let selectedDeviceId;
        const codeReader = new ZXing.BrowserQRCodeReader()
        console.log('ZXing code reader initialized')

        codeReader.getVideoInputDevices()
            .then((videoInputDevices) => {
                const sourceSelect = document.getElementById('sourceSelect')
                selectedDeviceId = videoInputDevices[0].deviceId
                if (videoInputDevices.length >= 1) {
                    videoInputDevices.forEach((element) => {
                        const sourceOption = document.createElement('option')
                        sourceOption.text = element.label
                        sourceOption.value = element.deviceId
                        sourceSelect.appendChild(sourceOption)
                    })

                    sourceSelect.onchange = () => {
                        selectedDeviceId = sourceSelect.value;
                    };

                    const sourceSelectPanel = document.getElementById('sourceSelectPanel')
                    sourceSelectPanel.style.display = 'block'
                }

                document.getElementById('startButton').addEventListener('click', () => {

                    decodeContinuously(codeReader, selectedDeviceId);

                    console.log(`Started decode from camera with id ${selectedDeviceId}`)
                })

                document.getElementById('resetButton').addEventListener('click', () => {
                    codeReader.reset()
                    document.getElementById('result').textContent = '';
                    console.log('Reset.')
                })

                document.getElementById('torchCheckbox').addEventListener('change',() => {
                    if($('#torchCheckbox').is(":checked")) {
                        enableTorch(codeReader, true)
                    } else {
                        enableTorch(codeReader, false)
                    }
                })

            })
            .catch((err) => {
                console.error(err)
            })
    })
</script>
@endsection
