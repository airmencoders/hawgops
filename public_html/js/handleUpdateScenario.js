const remapIconUrl = v1 => {
  switch (v1) {
    case '../chits/friendly/airbone.svg':
      return 'https://hawg-ops.com/static/media/airborne.df2cd266.svg'
    case '../chits/friendly/airborne-infantry.svg':
      return 'https://hawg-ops.com/static/media/airborne-infantry.a56050c6.svg'
    case '../chits/friendly/air-defense.svg':
      return 'https://hawg-ops.com/static/media/air-defense.af8b45b2.svg'
    case '../chits/friendly/anti-armor.svg':
      return 'https://hawg-ops.com/static/media/anti-armor.b34f832e.svg'
    case '../chits/friendly/armor.svg':
      return 'https://hawg-ops.com/static/media/armor.5e34d211.svg'
    case '../chits/friendly/artillery.svg':
      return 'https://hawg-ops.com/static/media/artillery.5cfadcbb.svg'
    case '../chits/friendly/aviation.svg':
      return 'https://hawg-ops.com/static/media/aviation.ce5c976a.svg'
    case '../chits/friendly/cbrne.svg':
      return 'https://hawg-ops.com/static/media/cbrne.b3d110bc.svg'
    case '../chits/friendly/counter-battery-radar.svg':
      return 'https://hawg-ops.com/static/media/counterbattery-radar.412bafde.svg'
    case '../chits/friendly/engineer.svg':
      return 'https://hawg-ops.com/static/media/engineer.d16ad5e6.svg'
    case '../chits/friendly/infantry.svg':
      return 'https://hawg-ops.com/static/media/9c99e29e.svg'
    case '../chits/friendly/light-armor.svg':
      return 'https://hawg-ops.com/static/media/e877e472.svg'
    case '../chits/friendly/maintenance.svg':
      return 'https://hawg-ops.com/static/media/maintenance.512b7c3a.svg'
    case '../chits/friendly/mech-infantry.svg':
      return 'https://hawg-ops.com/static/media/mech-infantry.8bacdfd9.svg'
    case '../chits/friendly/medical.svg':
      return 'https://hawg-ops.com/static/media/medical.a7456a27.svg'
    case '../chits/friendly/missile.svg':
      return 'https://hawg-ops.com/static/media/missile.4ce9a7b1.svg'
    case '../chits/friendly/mlrs.svg':
      return 'https://hawg-ops.com/static/media/mlrs.3bc1af8e.svg'
    case '../chits/friendly/recce.svg':
      return 'https://hawg-ops.com/static/media/recce.56176d11.svg'
    case '../chits/friendly/self-propelled-artillery.svg':
      return 'https://hawg-ops.com/static/media/self-propelled-artillery.4a8d1d95.svg'
    case '../chits/friendly/signals.svg':
      return 'https://hawg-ops.com/static/media/signals.c34f9425.svg'
    case '../chits/friendly/special-forces.svg':
      return 'https://hawg-ops.com/static/media/special-forces.28b09af1.svg'
    case '../chits/friendly/srv.svg':
      return 'https://hawg-ops.com/static/media/srv.23ace4d0.svg'
    case '../chits/friendly/supply.svg':
      return 'https://hawg-ops.com/static/media/supply.46765207.svg'
    case '../chits/friendly/unit.svg':
      return 'https://hawg-ops.com/static/media/unit.d8a35903.svg'
    case '../chits/friendly/wheeled-armor.svg':
      return 'https://hawg-ops.com/static/media/wheeled-armor.443d3fc'
    case '../chits/hostile/airbone.svg':
      return 'https://hawg-ops.com/static/media/airborne.38a043b7.svg'
    case '../chits/hostile/airborne-infantry.svg':
      return 'https://hawg-ops.com/static/media/airborne-infantry.7495274f.svg'
    case '../chits/hostile/air-defense.svg':
      return 'https://hawg-ops.com/static/media/air-defense.2f6bc0a9.svg'
    case '../chits/hostile/anti-armor.svg':
      return 'https://hawg-ops.com/static/media/anti-armor.0b70d1e6.svg'
    case '../chits/hostile/armor.svg':
      return 'https://hawg-ops.com/static/media/armor.8f32083e.svg'
    case '../chits/hostile/artillery.svg':
      return 'https://hawg-ops.com/static/media/artillery.7b3a9690.svg'
    case '../chits/hostile/aviation.svg':
      return 'https://hawg-ops.com/static/media/aviation.05f7fc8d.svg'
    case '../chits/hostile/cbrne.svg':
      return 'https://hawg-ops.com/static/media/cbrne.725c1dc5.svg'
    case '../chits/hostile/counter-battery-radar.svg':
      return 'https://hawg-ops.com/static/media/counterbattery-radar.7ec3e713.svg'
    case '../chits/hostile/engineer.svg':
      return 'https://hawg-ops.com/static/media/engineer.9fe6dd66.svg'
    case '../chits/hostile/infantry.svg':
      return 'https://hawg-ops.com/static/media/infantry.6137cbdf.svg'
    case '../chits/hostile/light-armor.svg':
      return 'https://hawg-ops.com/static/media/light-armor.1c44d584.svg'
    case '../chits/hostile/maintenance.svg':
      return 'https://hawg-ops.com/static/media/maintenance.29fbc2f8.svg'
    case '../chits/hostile/mech-infantry.svg':
      return 'https://hawg-ops.com/static/media/mech-infantry.306863c1.svg'
    case '../chits/hostile/medical.svg':
      return 'https://hawg-ops.com/static/media/medical.3ae34509.svg'
    case '../chits/hostile/missile.svg':
      return 'https://hawg-ops.com/static/media/missile.3ee84090.svg'
    case '../chits/hostile/mlrs.svg':
      return 'https://hawg-ops.com/static/media/mlrs.6517f49b.svg'
    case '../chits/hostile/recce.svg':
      return 'https://hawg-ops.com/static/media/recce.a6099e4c.svg'
    case '../chits/hostile/self-propelled-artillery.svg':
      return 'https://hawg-ops.com/static/media/self-propelled-artillery.0790c09d.svg'
    case '../chits/hostile/signals.svg':
      return 'https://hawg-ops.com/static/media/signals.14ad4e9c.svg'
    case '../chits/hostile/special-forces.svg':
      return 'https://hawg-ops.com/static/media/special-forces.49fc1351.svg'
    case '../chits/hostile/supply.svg':
      return 'https://hawg-ops.com/static/media/supply.41ffb33b.svg'
    case '../chits/hostile/unit.svg':
      return 'https://hawg-ops.com/static/media/unit.d5cc8d94.svg'
    case '../chits/hostile/wheeled-armor.svg':
      return 'https://hawg-ops.com/static/media/wheeled-armor.dea5279f.svg'
    case '../chits/ip/cp.svg':
      return 'https://hawg-ops.com/static/media/cp.07c4b44b.svg'
    case '../chits/ip/ip.svg':
      return 'https://hawg-ops.com/static/media/ip.9c70c67c.svg'
    case '../chits/ip/no-strike.svg':
      return 'https://hawg-ops.com/static/media/no-strike.f79b5590.svg'
    case '../chits/ip/tgt.svg':
      return 'https://hawg-ops.com/static/media/tgt.2daac1ea.svg'
    case '../chits/threats/ada.svg':
      return 'https://hawg-ops.com/static/media/ada.af746e84.svg'
    case '../chits/threats/missile.svg':
      return 'https://hawg-ops.com/static/media/missile.b14edbce.svg'
    case '../chits/friendly/horse-recce.svg':
    case '../chits/hostile/horse-recce.svg':
    default:
      console.error('Unidentified iconUrl:', v1)
      return null
  }
}

const remapThreat = v1 => {
  switch (v1) {
    case 'custom':
      return 0
    case 'SA-2B/F':
      return 1
    case 'SA-2D/E':
      return 2
    case 'SA-3':
      return 3
    case 'SA-5':
      return 4
    case 'SA-6':
      return 5
    case 'SA-8':
      return 6
    case 'SA-9':
      return 7
    case 'SA-10A/B':
      return 8
    case 'SA-11':
      return 9
    case 'SA-12A':
      return 10
    case 'SA-12B':
      return 11
    case 'SA-13':
      return 12
    case 'SA-15':
      return 13
    case 'SA-17':
      return 14
    case 'SA-19':
      return 15
    case 'SA-20':
      return 16
    case 'SA-21':
      return 17
    case 'SA-22':
      return 18
    default:
      console.error('Unrecognized', v1)
      return 0
  }
}

const remapSovereignty = v1 => {
  switch (v1) {
    case 'HOS':
      return 'Hostile'
    case 'SUS':
      return 'Suspect'
    case 'UNK':
      return 'Unknown'
    case 'FND':
      return 'Friendly'
    default:
      console.error('Unrecognized sovereignty', v1)
  }
}

const handleUpdateScenario = data => {
  let id = 0

  let scenario = {
    buildingLabels: [],
    bullseyes: [],
    circles: [],
    ellipses: [],
    friendlyMarkers: [],
    hostileMarkers: [],
    initialPoints: [],
    kineticPoints: [],
    lines: [],
    mapLabels: [],
    polygons: [],
    rectangles: [],
    survivors: [],
    styles: {
      mgrs: {
        gridzoneColor: '#ffa500',
        lineColor: '#ffffff',
      },
      gars: {
        cellColor: '#ffa500',
        quadrantColor: '#800080',
        keypadColor: '#ffffff'
      },
      buildingLabel: {
        color: '#ffff00',
      },
    },
    threatMarkers: [],
  }

  let json = data
  /*try {
    let object = JSON.parse(data)

    if (object && typeof object === 'text') {
      json = object
    }
  } catch (error) {
    console.error(error)
    console.error('There was an error loading the scenario')
  }*/

  if (json !== undefined) {
    // Get all the fields from the saved scenario
    let v1ThreatMarkers = json.threat_markers
    let v1Markers = json.markers
    let v1MapLabels = json.bldg_markers
    let v1Friendlymarkers = json.friendly_markers
    let v1HostileMarkers = json.hostile_markers
    let v1SurvivorMarkers = json.survivor_markers
    let v1Ellipses = json.ellipses
    let v1Lines = json.lines
    let v1Polygons = json.polygons
    let v1Rectangles = json.eas
    let v1Circles = json.rozs

    // Parse through all the Threat Markers
    v1ThreatMarkers.forEach(threat => {
      let data = null

      if (threat.data !== null) {
        data = {
          type: '9line',
          label: '',
          intent: threat.data.gfc_intent,
          typeMethod: threat.data.type_control,
          ip: threat.data.ip_hdg_dist,
          hdg: '',
          distance: '',
          elevation: Number.parseInt(threat.data.elevation.substring(0, threat.data.elevation.length - 3)),
          description: threat.data.description,
          location: threat.data.location_data,
          mark: threat.data.mark,
          friendlies: threat.data.friendlies,
          egress: threat.data.egress,
          remarks: threat.data.remarks_restrictions,
          tot: threat.data.tot,
          f2f: ''
        }
      }

      const v2 = {
        arty: {
          arty: false,
          display: false
        },
        iconType: 'div',
        layer: 'threat',
        title: threat.title,
        elevation: Number.parseInt(threat.elevation.substring(0, threat.elevation.length - 3)),
        threatType: remapThreat(threat.title),
        range: threat.units === 'm' ? threat.radius : threat.units === 'km' ? threat.radius / 1000 : threat.radius / 1852,
        unit: threat.units,
        sovereignty: remapSovereignty(threat.soverignty),
        color: threat.color,
        fill: false,
        fillColor: threat.color,
        label: threat.msnThreat,
        data: data,
        id: id,
        latlng: {
          lat: threat.latlng.lat,
          lng: threat.latlng.lng
        }
      }

      id++

      scenario = {
        ...scenario,
        threatMarkers: [...scenario.threatMarkers, v2]
      }
    })

    // Parse through all the markers
    v1Markers.forEach(marker => {
      let data = null

      if (marker.data !== null) {
        data = {
          type: '9line',
          label: '',
          intent: marker.data.gfc_intent,
          typeMethod: marker.data.type_control,
          ip: marker.data.ip_hdg_dist,
          hdg: '',
          distance: '',
          elevation: Number.parseInt(marker.data.elevation.substring(0, marker.data.elevation.length - 3)),
          description: marker.data.description,
          location: marker.data.location_data,
          mark: marker.data.mark,
          friendlies: marker.data.friendlies,
          egress: marker.data.egress,
          remarks: marker.data.remarks_restrictions,
          tot: marker.data.tot,
          f2f: ''
        }
      }

      let arty = {
        arty: false,
        display: false,
      }

      if (marker.icon.iconUrl.indexOf('artillery') !== -1 || marker.icon.iconUrl.indexOf('mlrs') !== -1) {
        arty = {
          arty: true,
          display: true,
        }
      }

      let v2 = {
        arty: arty,
        elevation: Number.parseInt(marker.elevation.substring(0, marker.elevation.length - 3)),
        iconType: 'img',
        iconUrl: remapIconUrl(marker.icon.iconUrl),
        title: marker.title,
        id: id,
        latlng: {
          lat: marker.latlng.lat,
          lng: marker.latlng.lng
        }
      }

      id++

      if (marker.icon.iconUrl.indexOf('friendly') !== -1) {
        v2 = {
          ...v2,
          layer: 'friendly',
        }

        scenario = {
          ...scenario,
          friendlyMarkers: [...scenario.friendlyMarkers, v2]
        }
      } else if (marker.icon.iconUrl.indexOf('hostile') !== -1) {
        v2 = {
          ...v2,
          data: data,
          layer: 'hostile',
        }

        scenario = {
          ...scenario,
          hostileMarkers: [...scenario.hostileMarkers, v2]
        }
      } else {
        v2 = {
          ...v2,
          layer: 'ip',
        }

        scenario = {
          ...scenario,
          initialPoints: [...scenario.initialPoints, v2]
        }
      }
    })

    // Parse through all the building labels (map labels in v2)
    v1MapLabels.forEach(label => {

      let v2 = {
        arty: {
          arty: false,
          display: false,
        },
        elevation: Number.parseInt(label.elevation.substring(0, label.elevation.length - 3)),
        iconType: 'div',
        layer:'mapLabel',
        color: '#ff0000',
        iconUrl: null,
        fontSize: '30',
        lineHeight: '30px',
        title: label.title,
        id: id,
        latlng: {
          lat: label.latlng.lat,
          lng: label.latlng.lng
        }
      }

      id++

      scenario = {
        ...scenario,
        mapLabels: [...scenario.mapLabels, v2]
      }
    })

    // Parse through all the friendly markers
    v1Friendlymarkers.forEach(marker => {
      let arty = {
        arty: false,
        display: false,
      }

      if (marker.icon.iconUrl.indexOf('artillery') !== -1 || marker.icon.iconUrl.indexOf('mlrs') !== -1) {
        arty = {
          arty: true,
          display: true,
        }
      }

      let v2 = {
        arty: arty,
        elevation: Number.parseInt(marker.elevation.substring(0, marker.elevation.length - 3)),
        iconType: 'img',
        layer:'friendly',
        iconUrl: remapIconUrl(marker.icon.iconUrl),
        title: marker.title,
        id: id,
        latlng: {
          lat: marker.latlng.lat,
          lng: marker.latlng.lng
        }
      }

      id++

      scenario = {
        ...scenario,
        friendlyMarkers: [...scenario.friendlyMarkers, v2]
      }
    })

    // Parse through all the hostile markers
    v1HostileMarkers.forEach(marker => {
      let data = null

      if (marker.data !== null) {
        data = {
          type: '9line',
          label: '',
          intent: marker.data.gfc_intent,
          typeMethod: marker.data.type_control,
          ip: marker.data.ip_hdg_dist,
          hdg: '',
          distance: '',
          elevation: Number.parseInt(marker.data.elevation.substring(0, marker.data.elevation.length - 3)),
          description: marker.data.description,
          location: marker.data.location_data,
          mark: marker.data.mark,
          friendlies: marker.data.friendlies,
          egress: marker.data.egress,
          remarks: marker.data.remarks_restrictions,
          tot: marker.data.tot,
          f2f: ''
        }
      }

      let arty = {
        arty: false,
        display: false,
      }

      if (marker.icon.iconUrl.indexOf('artillery') !== -1 || marker.icon.iconUrl.indexOf('mlrs') !== -1) {
        arty = {
          arty: true,
          display: true,
        }
      }

      let v2 = {
        arty: arty,
        data: data,
        elevation: Number.parseInt(marker.elevation.substring(0, marker.elevation.length - 3)),
        iconType: 'img',
        layer:'hostile',
        iconUrl: remapIconUrl(marker.icon.iconUrl),
        title: marker.title,
        id: id,
        latlng: {
          lat: marker.latlng.lat,
          lng: marker.latlng.lng
        }
      }

      id++

      scenario = {
        ...scenario,
        hostileMarkers: [...scenario.hostileMarkers, v2]
      }
    })

    // Parse through all the survivor markers
    v1SurvivorMarkers.forEach(marker => {
      let data = null

      if (marker.data !== null) {
        let callsignFreq = marker.data.callsign_freq.split('/')
        data = {
          type: '15line',
          callsign: callsignFreq[0],
          frequency: callsignFreq[1],
          plsHhrid: marker.data.pls_hhrid,
          numObjectives: marker.data.num_objectives,
          location: marker.data.mgrs,
          elevation: Number.parseInt(marker.data.elevation.substring(0, marker.data.elevation.length - 3)),
          dateTime: marker.data.dtg,
          source: marker.data.source,
          condition: marker.data.condition,
          equipment: marker.data.equipment,
          authentication: marker.data.authentication,
          threats: marker.data.threats,
          pzDescription: marker.data.pz_description,
          oscFreq: marker.data.rv_freq,
          ipHdg: marker.data.ip_ingress,
          rescort: marker.data.rescort,
          gameplan: marker.data.obj_gp,
          signal: marker.data.signal,
          egress: marker.data.egress_rte,
        }
      }

      let v2 = {
        arty: {
          arty: false,
          display: false,
        },
        data: data,
        elevation: Number.parseInt(marker.elevation.substring(0, marker.elevation.length - 3)),
        iconType: 'img',
        layer:'survivor',
        iconUrl: remapIconUrl(marker.icon.iconUrl),
        title: marker.title,
        id: id,
        latlng: {
          lat: marker.latlng.lat,
          lng: marker.latlng.lng
        }
      }

      id++

      scenario = {
        ...scenario,
        survivors: [...scenario.survivors, v2]
      }
    })

    // Parse through all the ellipses
    v1Ellipses.forEach(ellipse => {
      let v2 = {
        center: {
          lat: ellipse.latlng.lat,
          lng: ellipse.latlng.lng,
        },
        color: ellipse.color,
        dashArray: null,
        fillColor: null,
        layer: 'ellipse',
        length: ellipse.radii.x,
        tilt: ellipse.tilt,
        title: ellipse.title,
        width: ellipse.radii.y,
        id: id,
        elevation: 'Pending',
      }

      id++

      scenario = {
        ...scenario,
        ellipses: [...scenario.ellipses, v2]
      }
    })

    // Parse through all the lines
    v1Lines.forEach(line => {
      let v2 = {
        color: line.color,
        dashArray: null,
        layer: 'line',
        positions: [...line.latlngs],
        title: '',
        id: id,
        elevation: 'Pending',
      }

      id++

      scenario = {
        ...scenario,
        lines: [...scenario.lines, v2]
      }
    })

    // Parse through all the polygons
    v1Polygons.forEach(polygon=> {
      let v2 = {
        color: '#4a90e2',
        dashArray: null,
        fillColor: null,
        layer: 'polygon',
        positions: [...polygon.latlngs],
        title: polygon.title,
        id: id,
        elevation: 'Pending',
      }

      id++

      scenario = {
        ...scenario,
        polygons: [...scenario.polygons, v2]
      }
    })

    // Parse through all the rectangles
    v1Rectangles.forEach(rectangle => {
      let v2 = {
        bounds: [rectangle.latlngs[0][1], rectangle.latlngs[0][3]],
        color: '#4a90e2',
        dashArray: null,
        fillColor: null,
        layer: 'rectangle',
        title: rectangle.title,
        id: id,
        elevation: 'Pending',
      }

      id++

      scenario = {
        ...scenario,
        rectangles: [...scenario.rectangles, v2]
      }
    })

    // Parse through all the circles
    v1Circles.forEach(circle => {
      let v2 = {
        color: '#4a90e2',
        dashArray: null,
        fillColor: null,
        latlng: {
          lat: circle.latlng.lat,
          lng: circle.latlng.lng,
        },
        layer: 'circle',
        radius: circle.radius,
        title: circle.title,
        unit: 'm',
        id: id,
        elevation: 'Pending',
      }

      id++
      
      scenario = {
        ...scenario,
        circles: [...scenario.circles, v2]
      }
    })

    let completed = {
      name: data.name,
      classification: 'UNCLASSIFIED',
      date: new Date(),
      data: {
        ...scenario,
        data: {
          buildingLabel: 1,
          firstLetter: 65,
          markerId: id,
          secondLetter: 65,
        },
      }
    }

    return completed
  } 
}

export { handleUpdateScenario }