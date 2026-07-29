/**
 * Fichier JavaScript pour le Tableau de Bord (Dashboard)
 * Génération du graphique des ventes avec Highcharts (Sécurisé et Accessible)
 */
document.addEventListener("DOMContentLoaded", () => {
    
    const chartContainer = document.getElementById('salesChartContainer');
    
    if (chartContainer) {
        // 1. Récupération sécurisée des données depuis les attributs HTML (Data-Attributes)
        const rawKeys = chartContainer.getAttribute('data-keys');
        const rawValues = chartContainer.getAttribute('data-values');
        
        let categories = [];
        let seriesData = [];

        try {
            categories = JSON.parse(rawKeys);
            // Conversion des valeurs stringifiées en nombres entiers pour Highcharts
            seriesData = JSON.parse(rawValues).map(Number);
        } catch (error) {
            console.error("Erreur lors de l'analyse des données de statistiques :", error);
            return;
        }

        // 2. Initialisation de Highcharts avec des options d'accessibilité et de design responsif
        Highcharts.chart('salesChartContainer', {
            chart: {
                type: 'line',
                style: {
                    fontFamily: 'inherit' // Hérite de la police du panel d'administration ($font-main)
                }
            },
            title: {
                text: 'Statistiques des ventes (7 derniers jours)',
                x: -20
            },
            subtitle: {
                text: "Aperçu global de l'activité de la boutique",
                x: -20
            },
            xAxis: {
                categories: categories,
                title: { text: 'Dates' }
            },
            yAxis: {
                title: {
                    text: 'Nombre de commandes'
                },
                plotLines: [{
                    value: 0,
                    width: 1,
                    color: '#dc2626'
                }],
                allowDecimals: false // Empêche l'affichage des décimales (ex: "0.5 commande")
            },
            tooltip: {
                valueSuffix: ' commande(s)'
            },
            legend: {
                layout: 'vertical',
                align: 'right',
                verticalAlign: 'middle',
                borderWidth: 0
            },
            series: [{
                name: 'Ventes confirmées',
                data: seriesData,
                color: '#0ea5e9', // Bleu moderne adapté au panel
                marker: {
                    enabled: true,
                    radius: 5
                }
            }],
            credits: {
                enabled: false // Masque le logo Highcharts pour un rendu professionnel
            }
        });
    }
});