<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chatbot</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css">
</head>
<body>
    <div class="container mt-5">
        <div class="card">
            <div class="card-header">
                <h1>Chatbot</h1>
            </div>
            <div class="card-body">
                <form action="{{ url('/chatbot') }}" method="post">
                    @csrf
                    <div class="form-group">
                        <label for="question">Pregunta:</label>
                        <input type="text" class="form-control" id="question" name="question" required>
                    </div>

                    <div class="form-group">
                        <label for="location">Ubicación:</label>
                        <div id="pano" style="height: 300px;"></div>
                    </div>

                    <button type="submit" class="btn btn-primary">Obtener Respuesta</button>
                </form>
                
                @if(isset($chatbotResponse))
                    <h2>Respuesta del Chatbot:</h2>
                    <p>{{ $chatbotResponse }}</p>
                @endif
            </div>
        </div>
    </div>

    <script>
        function initMap() {
            // Inicializa Street View en una ubicación genérica
            const panorama = new google.maps.StreetViewPanorama(
                document.getElementById('pano'), {
                    position: { lat: 0, lng: 0 },
                    pov: { heading: 165, pitch: 0 },
                    zoom: 1
                }
            );

            // Verifica si hay coordenadas proporcionadas por el servidor
            @if(isset($coordinates))
                // Actualiza la posición de Street View con las coordenadas
                panorama.setPosition(new google.maps.LatLng({{ $coordinates['lat'] }}, {{ $coordinates['lng'] }}));
            @endif
        }
    </script>

</body>
</html>
