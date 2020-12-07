const createChart = (id, XLabels, dataset, title, legendLabel, xtitle, ytitle) => {
    let myChart = new Chart(id, {
        type: 'bar',
        data: {
            labels: XLabels,
            datasets: [{
                label: legendLabel,
                data: dataset,
                backgroundColor: 'rgba(131, 195, 169, 0.3)',
                borderColor: 'rgba(131, 195, 169, 1)',
                borderWidth: 2
            }]
        },
        options: {
            title: {
                display: true,
                fontSize: 24,
                fontColor: ' #f1471d',
                fontFamily: 'Barlow, sans-serif',
                text: title
            },
            legend: {
                display: true,
                onClick: (e) => e.stopPropagation,
                labels: {
                    fontSize: 18,
                    fontFamily: 'Barlow, sans-serif'
                }

            },
            scales: {
                display: true,
                labelString: "Cantidad",
                xAxes: [{
                    display: true,
                    scaleLabel: {
                        display: true,
                        labelString: xtitle,
                        fontFamily: 'Barlow, sans-serif',
                        fontStyle: 'bold',
                        fontSize: 16
                    }
                }],
                yAxes: [{
                    display: true,
                    scaleLabel: {
                        display: true,
                        labelString: ytitle,
                        fontFamily: 'Barlow, sans-serif',
                        fontStyle: 'bold',
                        fontSize: 16
                    },
                    ticks: {
                        beginAtZero: true
                    }
                }]
            }
        }
    });

    return myChart;
}


const updateChart = (chart, XLabels, dataset) => {
    chart.data.labels = XLabels;
    chart.data.datasets[0].data = dataset;
    chart.update();
}

//CAMBIAR DE SUCURSAL
const confirmDelete = () => {

    if (carritoProducts.length > 0) {
        let modal = document.getElementById('custom-modal');
        let msg = document.getElementById('modal-msg');
        let icon = document.getElementById('msg-icon');
        let noButton = document.getElementsByClassName('ok-button');

        msg.innerHTML = 'Sus productos agregados serán borrados, desea continuar?'
        icon.src = "https://img.icons8.com/officel/100/000000/high-risk.png"

        noButton[0].style.display = 'inline-block';
        noButton[0].innerHTML = 'NO';

        noButton[1].style.display = 'inline-block';
        noButton[1].innerHTML = 'SI';

        modal.style.display = 'block';
    } else {
        location.href = '../../procesos/reselectSucursal.php';
    }
}


//CERRAR MODAL
const closeModal = () => {
    let modal = document.getElementById('custom-modal');
    modal.style.display = "none";
}

//AGREGAR AL CARRITO
const agregarCarrito = (id) => {
    let noButton = document.getElementsByClassName('ok-button');
    let modal = document.getElementById('custom-modal');
    let msg = document.getElementById('modal-msg');

    noButton[0].style.display = 'inline-block';
    noButton[0].innerHTML = 'OK';

    noButton[1].style.display = 'none';


    if (carritoProducts == null || carritoProducts == undefined || carritoProducts == "") {
        carritoProducts = [];
        carritoProducts.push(id);
        window.localStorage.setItem('carrito', JSON.stringify(carritoProducts));
        compras[0].innerHTML = 1;
        msg.innerHTML = 'El producto fue agregado al carrito.'
    } else {
        let duplicate = false;
        carritoProducts = JSON.parse(window.localStorage.getItem('carrito'));
        for (let i = 0; i < carritoProducts.length; i++) {
            if (id == carritoProducts[i]) {
                duplicate = true;
                break;
            }
        }
        if (duplicate) {
            document.getElementById('msg-icon').src = "https://img.icons8.com/officel/100/000000/high-risk.png";
            msg.innerHTML = 'Ya el producto está agregado en el carrito.'
        } else {
            document.getElementById('msg-icon').src = "https://img.icons8.com/flat_round/100/000000/checkmark.png";
            carritoProducts.push(id);
            window.localStorage.setItem('carrito', JSON.stringify(carritoProducts));
            compras[0].innerHTML = carritoProducts.length;
            msg.innerHTML = 'El producto fue agregado al carrito.'

        }
    }

    modal.style.display = 'block';
}