/**
 * LivreurGpsTracker - Module de Géolocalisation & Tracé d'Itinéraire Temps Réel LAVEX
 * Trace la route (trajet) entre la moto du livreur et son point d'arrivée (Client / Pressing).
 * Affiche la distance restante et le temps de trajet estimé en direct.
 */

window.LivreurGpsTracker = (function() {
    let watchId = null;
    let myLiveMarker = null;
    let myLiveAccuracyCircle = null;
    let destMarker = null;
    let routePolyline = null;
    let isTracking = false;
    let isFollowMode = true;
    let activeMap = null;
    let currentDestination = null; // { lat, lng, name, address, type }
    let lastRouteFetchTime = 0;

    function init(mapInstance, livreurCode, hasActiveMission, activeDest) {
        activeMap = mapInstance;
        currentDestination = activeDest || null;

        if (!hasActiveMission) {
            stop();
            return;
        }

        if (!navigator.geolocation) {
            console.warn('[LivreurGpsTracker] Géolocalisation non supportée.');
            return;
        }

        start(mapInstance);
    }

    function start(mapInstance) {
        if (watchId !== null) return;
        activeMap = mapInstance || activeMap;
        isTracking = true;

        // Si destination configurée, afficher le marqueur d'arrivée
        if (currentDestination && activeMap && typeof L !== 'undefined') {
            drawDestinationMarker(currentDestination);
        }

        const options = {
            enableHighAccuracy: true,
            timeout: 15000,
            maximumAge: 0
        };

        watchId = navigator.geolocation.watchPosition(
            function(position) {
                handlePositionSuccess(position);
            },
            function(error) {
                handlePositionError(error);
            },
            options
        );

        updateGpsBadge(true, 'GPS En Route • Recherche signal...');
    }

    function stop() {
        if (watchId !== null) {
            navigator.geolocation.clearWatch(watchId);
            watchId = null;
        }
        isTracking = false;

        // Nettoyage des éléments visuels GPS & Itinéraire
        if (myLiveMarker && activeMap) {
            activeMap.removeLayer(myLiveMarker);
            myLiveMarker = null;
        }
        if (myLiveAccuracyCircle && activeMap) {
            activeMap.removeLayer(myLiveAccuracyCircle);
            myLiveAccuracyCircle = null;
        }
        if (routePolyline && activeMap) {
            activeMap.removeLayer(routePolyline);
            routePolyline = null;
        }
        if (destMarker && activeMap) {
            activeMap.removeLayer(destMarker);
            destMarker = null;
        }

        removeGpsBadge();
        removeRouteInfoHud();
    }

    function setDestination(dest) {
        currentDestination = dest;
        if (activeMap && typeof L !== 'undefined') {
            drawDestinationMarker(dest);
            if (myLiveMarker) {
                const curPos = myLiveMarker.getLatLng();
                drawRoutePolyline(curPos.lat, curPos.lng, dest.lat, dest.lng);
            }
        }
    }

    function handlePositionSuccess(position) {
        const lat = position.coords.latitude;
        const lng = position.coords.longitude;
        const accuracy = position.coords.accuracy || 15;
        const speed = position.coords.speed || 0;
        const speedKmh = Math.round(speed * 3.6);

        updateGpsBadge(true, `En Course (${speedKmh} km/h • ±${Math.round(accuracy)}m)`);

        // Mise à jour de la moto en direct
        if (activeMap && typeof L !== 'undefined') {
            updateMapLiveMarker(activeMap, lat, lng, accuracy, speedKmh);

            // Mise à jour du tracé d'itinéraire vers la destination
            if (currentDestination && currentDestination.lat && currentDestination.lng) {
                const now = Date.now();
                if (now - lastRouteFetchTime > 6000 || !routePolyline) {
                    drawRoutePolyline(lat, lng, currentDestination.lat, currentDestination.lng);
                    lastRouteFetchTime = now;
                }
            }
        }
    }

    function handlePositionError(error) {
        let msg = 'Signal GPS indisponible';
        if (error.code === error.PERMISSION_DENIED) {
            msg = 'Veuillez autoriser le GPS pour le guidage';
        }
        console.warn('[LivreurGpsTracker]', msg);
        updateGpsBadge(false, msg);
    }

    function updateMapLiveMarker(map, lat, lng, accuracy, speedKmh) {
        const latLng = [lat, lng];

        const liveDriverIcon = L.divIcon({
            className: 'live-driver-marker-container',
            html: `
                <div class="live-driver-pulse"></div>
                <div class="live-driver-icon" title="Ma position en direct (${speedKmh} km/h)">
                    <i class="fa fa-motorcycle" style="color: #FFF; font-size: 14px;"></i>
                </div>
            `,
            iconSize: [36, 36],
            iconAnchor: [18, 18],
            popupAnchor: [0, -20]
        });

        if (!myLiveMarker) {
            myLiveMarker = L.marker(latLng, { icon: liveDriverIcon, zIndexOffset: 1000 }).addTo(map);
            myLiveMarker.bindPopup(`
                <div style="font-family: inherit; font-size: 13px; text-align: center; padding: 4px;">
                    <strong style="color: #1E3A5F; font-size: 14px; display: block;">🛵 Ma Position en Direct</strong>
                    <span style="color: #059669; font-weight: 700; font-size: 12px;">En déplacement (${speedKmh} km/h)</span>
                    <div style="font-size: 11px; color: #64748B; margin-top: 4px;">Précision GPS : ±${Math.round(accuracy)}m</div>
                </div>
            `);

            myLiveAccuracyCircle = L.circle(latLng, {
                radius: Math.min(accuracy, 100),
                color: '#2563EB',
                fillColor: '#3B82F6',
                fillOpacity: 0.15,
                weight: 1
            }).addTo(map);

            if (isFollowMode) {
                map.setView(latLng, 15);
            }
        } else {
            myLiveMarker.setLatLng(latLng);
            if (myLiveAccuracyCircle) {
                myLiveAccuracyCircle.setLatLng(latLng);
                myLiveAccuracyCircle.setRadius(Math.min(accuracy, 100));
            }

            if (isFollowMode) {
                map.panTo(latLng);
            }
        }
    }

    function drawDestinationMarker(dest) {
        if (!activeMap || !dest || !dest.lat || !dest.lng) return;

        const destIcon = L.divIcon({
            className: 'dest-marker-container',
            html: `
                <div style="width: 38px; height: 38px; border-radius: 50%; background: #DC2626; border: 3px solid #FFF; box-shadow: 0 4px 12px rgba(220,38,38,0.5); display: flex; align-items: center; justify-content: center; color: #FFF; font-size: 16px;">
                    <i class="fa fa-flag-checkered"></i>
                </div>
            `,
            iconSize: [38, 38],
            iconAnchor: [19, 19],
            popupAnchor: [0, -22]
        });

        if (!destMarker) {
            destMarker = L.marker([dest.lat, dest.lng], { icon: destIcon, zIndexOffset: 990 }).addTo(activeMap);
            destMarker.bindPopup(`
                <div style="font-family: inherit; font-size: 13px; line-height: 1.5; min-width: 180px;">
                    <strong style="color: #DC2626; font-size: 14px; display: block;">🏁 Point d'Arrivée (Destination)</strong>
                    <div style="margin: 6px 0 2px; color: #1E293B;"><strong>${dest.name || 'Client'}</strong></div>
                    <small style="color: #64748B;">${dest.address || 'Abidjan'}</small>
                </div>
            `);
        } else {
            destMarker.setLatLng([dest.lat, dest.lng]);
        }
    }

    /**
     * Calcule et trace la vraie route par les rues d'Abidjan via l'API OSRM
     */
    function drawRoutePolyline(startLat, startLng, endLat, endLng) {
        if (!activeMap) return;

        const osrmUrl = `https://router.project-osrm.org/route/v1/driving/${startLng},${startLat};${endLng},${endLat}?overview=full&geometries=geojson`;

        $.getJSON(osrmUrl)
            .done(function(data) {
                if (data.routes && data.routes.length > 0) {
                    const route = data.routes[0];
                    const coords = route.geometry.coordinates.map(c => [c[1], c[0]]); // [lat, lng]
                    const distanceKm = (route.distance / 1000).toFixed(1);
                    const durationMin = Math.ceil(route.duration / 60);

                    renderPolyline(coords, '#2563EB');
                    updateRouteInfoHud(distanceKm, durationMin, currentDestination);
                } else {
                    fallbackDirectPolyline(startLat, startLng, endLat, endLng);
                }
            })
            .fail(function() {
                fallbackDirectPolyline(startLat, startLng, endLat, endLng);
            });
    }

    function fallbackDirectPolyline(startLat, startLng, endLat, endLng) {
        const coords = [[startLat, startLng], [endLat, endLng]];
        renderPolyline(coords, '#D97706');
        const distDirectKm = calculateDistanceKm(startLat, startLng, endLat, endLng).toFixed(1);
        const estMin = Math.ceil(distDirectKm * 2.5);
        updateRouteInfoHud(distDirectKm, estMin, currentDestination);
    }

    function renderPolyline(latLngs, color) {
        if (!activeMap) return;

        if (!routePolyline) {
            routePolyline = L.polyline(latLngs, {
                color: color || '#2563EB',
                weight: 6,
                opacity: 0.85,
                lineJoin: 'round',
                dashArray: null
            }).addTo(activeMap);
        } else {
            routePolyline.setLatLngs(latLngs);
            routePolyline.setStyle({ color: color });
        }
    }

    function calculateDistanceKm(lat1, lon1, lat2, lon2) {
        const R = 6371; // Rayon de la Terre en km
        const dLat = (lat2 - lat1) * Math.PI / 180;
        const dLon = (lon2 - lon1) * Math.PI / 180;
        const a = Math.sin(dLat/2) * Math.sin(dLat/2) +
                  Math.cos(lat1 * Math.PI / 180) * Math.cos(lat2 * Math.PI / 180) *
                  Math.sin(dLon/2) * Math.sin(dLon/2);
        const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1-a));
        return R * c;
    }

    function updateRouteInfoHud(distanceKm, durationMin, dest) {
        let hud = document.getElementById('livreur-route-hud');
        if (!hud) {
            hud = document.createElement('div');
            hud.id = 'livreur-route-hud';
            hud.style.cssText = `
                position: absolute;
                top: 14px;
                left: 60px;
                z-index: 1000;
                background: rgba(30, 58, 95, 0.95);
                backdrop-filter: blur(8px);
                color: #FFFFFF;
                padding: 10px 16px;
                border-radius: 12px;
                font-family: inherit;
                box-shadow: 0 8px 24px rgba(0,0,0,0.25);
                border: 1px solid rgba(255,255,255,0.15);
                max-width: 320px;
                display: flex;
                flex-direction: column;
                gap: 4px;
                animation: fadeIn 0.3s ease;
            `;
            const mapEl = document.getElementById('tourneeMap') || document.body;
            mapEl.parentElement.appendChild(hud);
        }

        const isArrived = parseFloat(distanceKm) < 0.05;

        if (isArrived) {
            hud.innerHTML = `
                <div style="display: flex; align-items: center; gap: 8px; color: #10B981; font-weight: 800; font-size: 14px;">
                    <i class="fa fa-check-circle" style="font-size: 18px;"></i>
                    <span>Vous êtes arrivé à destination !</span>
                </div>
                <div style="font-size: 12px; color: #E2E8F0;">${dest ? (dest.name + ' • ' + dest.address) : ''}</div>
            `;
        } else {
            hud.innerHTML = `
                <div style="display: flex; justify-content: space-between; align-items: center; gap: 12px;">
                    <span style="font-size: 11px; font-weight: 700; color: #93C5FD; text-transform: uppercase; letter-spacing: 0.5px;">
                        Destination en cours
                    </span>
                    <span style="font-size: 12px; font-weight: 800; background: #2563EB; padding: 2px 8px; border-radius: 10px;">
                        ~${durationMin} min
                    </span>
                </div>
                <div style="font-size: 13px; font-weight: 700; color: #FFF; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                    ${dest ? dest.name : 'Client'}
                </div>
                <div style="font-size: 11px; color: #CBD5E1; display: flex; align-items: center; justify-content: space-between; gap: 8px; margin-top: 2px;">
                    <span><i class="fa fa-map-marker-alt" style="color: #EF4444;"></i> ${dest ? dest.address : 'Abidjan'}</span>
                    <strong style="color: #FBBF24; font-size: 12px;">${distanceKm} km</strong>
                </div>
            `;
        }
    }

    function removeRouteInfoHud() {
        const hud = document.getElementById('livreur-route-hud');
        if (hud) hud.remove();
    }

    function updateGpsBadge(active, text) {
        let badge = document.getElementById('livreur-gps-live-badge');
        if (!badge) {
            badge = document.createElement('div');
            badge.id = 'livreur-gps-live-badge';
            badge.style.cssText = `
                position: fixed;
                bottom: 20px;
                right: 20px;
                z-index: 99999;
                background: #059669;
                color: #FFFFFF;
                padding: 8px 14px;
                border-radius: 30px;
                font-size: 12px;
                font-weight: 700;
                display: flex;
                align-items: center;
                gap: 8px;
                box-shadow: 0 4px 12px rgba(0,0,0,0.18);
                font-family: inherit;
                transition: all 0.3s ease;
            `;
            document.body.appendChild(badge);
        }

        badge.style.background = active ? '#059669' : '#DC2626';
        badge.innerHTML = `
            <span style="width: 8px; height: 8px; border-radius: 50%; background: #FFF; display: inline-block; animation: pulse 1.5s infinite;"></span>
            <span>${text}</span>
        `;
    }

    function removeGpsBadge() {
        const badge = document.getElementById('livreur-gps-live-badge');
        if (badge) badge.remove();
    }

    function toggleFollowMode() {
        isFollowMode = !isFollowMode;
        if (typeof showToast === 'function') {
            showToast(isFollowMode ? 'Centrage automatique GPS activé' : 'Centrage automatique GPS désactivé', 'info');
        }
        return isFollowMode;
    }

    return {
        init: init,
        start: start,
        stop: stop,
        setDestination: setDestination,
        toggleFollowMode: toggleFollowMode
    };
})();
