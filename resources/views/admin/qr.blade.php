@extends('layouts.app')
@section('content')

<script>
    setActive("qrcode");
</script>
<div class="row mt-5 mt-sm-1">
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
                        <a class="btn btn-primary" id="resetButton">Stop</a>
                    </div>
                    <div class=" form-switch my-1 justify-content-center" style="transform: scale(1.5)">
                        <input class="form-check-input"  type="checkbox" role="switch" id="torchCheckbox">
                        <label class="form-check-label"  for="flexSwitchCheckDefault">Zaklamp</label>
                    </div>
                    <div id="sourceSelectPanel" style="display:none" class="center mt-2">
                        <select id="sourceSelect" class="form-select form-select-sm" style="max-width:400px">

                        </select>
                    </div>
                    <div class="mt-2" id="video-container" style="display: none">
                        <video id="video" width="100%" class="qr-code-video"></video>
                    </div>
                    <div class="card-body mx-auto participantCard text-left mt-2" style="max-width: 400px" id="particpant-card">
                        <ul class="list-group">
                            <li class="list-group-item" id="allowed">Informatie: </li>
                            <li class="list-group-item" id="name">Naam: </li>
                            <li class="list-group-item" id="age">Leeftijd: </li>
                        </ul>
                    </div>
                </div>
            </section>
        </main>
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
            nameElement.textContent = "Naam " + user.firstName + " " + user.insertion + " " + user.lastName
        } else  {
            nameElement.textContent = "Naam: " + user.firstName + " " + user.lastName
        }
        allowElement.textContent = "Informatie: " + allowed;
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
                if(check.checked) {
                    $.ajax({
                        url: '/participants/' + result.text + "/get",
                        type: 'GET',
                        success: function(response) {
                            obj = JSON.parse(response)
                            console.log(obj)
                            if(obj.above18){
                                console.log('ja')
                                document.getElementById('particpant-card').classList.remove('underEightTeen');
                                document.getElementById('particpant-card').classList.add('aboveEightTeen');

                                document.getElementById('age').style.backgroundColor = '#A1F8A1FF';
                            } else {
                                console.log('nee')
                                document.getElementById('particpant-card').classList.remove('aboveEightTeen');
                                document.getElementById('particpant-card').classList.add('underEightTeen');

                                document.getElementById('age').style.backgroundColor = '#FD7272FF';
                            }
                            if(obj.removedFromIntro){
                                setInformation(obj, "niet toestaan, permanent verwijderd");
                                document.getElementById('particpant-card').classList.remove('aboveEightTeenQR');
                                document.getElementById('particpant-card').classList.add('underEightTeenQR');
                                flashBackgroundRed();
                                return;
                            }
                            if(!obj.haspaid) {
                                setInformation(obj, "niet toestaan, niet betaald");
                                document.getElementById('particpant-card').classList.remove('aboveEightTeenQR');
                                document.getElementById('particpant-card').classList.add('underEightTeenQR');
                                flashBackgroundRed();
                                return;
                            }
                            setInformation(obj, "ja");

                            if(!obj.checkedIn) {
                                $.ajax({
                                    url: '/participants/' + result.text + "/checkIn",
                                    type: 'POST',
                                    success: function (response) {
                                        flashBackgroundGreen();
                                    },
                                    beforeSend: function (request) {
                                        return request.setRequestHeader('X-CSRF-Token', $("meta[name='csrf-token']").attr('content'));
                                    }
                                });
                            }
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
                                    setInformation(obj,"Uitgechecked!");
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
                    document.getElementById('video-container').style.display = 'block';
                    console.log(`Started decode from camera with id ${selectedDeviceId}`)
                })

                document.getElementById('resetButton').addEventListener('click', () => {
                    codeReader.reset()
                    document.getElementById('video-container').style.display = 'none';
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
