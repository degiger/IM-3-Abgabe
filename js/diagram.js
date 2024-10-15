document.addEventListener("DOMContentLoaded", async function () {
  let place = document.querySelector("#weekDay-Pedestrians").dataset.place;
  console.log(place);
  // Fetch the data from the API
  async function fetchData() {
      const response = await fetch(`https://etl.mmp.li/sunnigsluzern/etl/unload.php?ort=${place}`);
      const data = await response.json();
      return data;
  }

  // Process and filter the data by weekday
  function filterDataByWeekday(data, selectedWeekday) {
      return data.filter(item => item.weekday == selectedWeekday);
  }

  // Render the Chart.js bar chart
  function renderChart(data) {
      const ctx = document.getElementById('weekDay-Pedestrians').getContext('2d');

      const labels = data.map(item => `${item.hour}:00`);
      const values = data.map(item => parseFloat(item.median_counter));

      if (window.myBarChart) {
          window.myBarChart.destroy();
      }

      window.myBarChart = new Chart(ctx, {
          type: 'bar',
          data: {
              labels: labels,
              datasets: [{
                  label: 'Median Pedestrian Count',
                  data: values,
                  backgroundColor: '#FFD200',
                  borderColor: '#ffff',
                  borderWidth: 1,
                  borderRadius: 5,  // round corners
                  barPercentage: 0.6,  // adjust the width of the bars
              }]
          },
          options: {
              responsive: true,
              plugins: {
                  legend: {
                      display: false // hide the legend
                  },
                  tooltip: {
                      callbacks: {
                          label: function(tooltipItem) {
                              // Custom tooltip to show hour and pedestrian count
                              return `${tooltipItem.label} Uhr: ${tooltipItem.raw} Besucher`;
                          }
                      }
                  }
              },
              scales: {
                  x: {
                      ticks: {
                          font: {
                              size: 14,
                              weight: 'bold',
                          },
                          color: '#ffff',
                      },
                      grid: {
                          display: false,  // remove the vertical grid lines
                      },
                  },
                  y: {
                      beginAtZero: true,
                      ticks: {
                          font: {
                              size: 12,
                          },
                          color: '#ffff',  // light gray for y-axis labels
                      },
                      grid: {
                          color: '#f0f0f0',  // light gray grid lines
                      },
                  }
              }
          }
      });
  }

  // Initial chart render
  const data = await fetchData();
  const selectElement = document.getElementById('weekday-select');

  selectElement.addEventListener('change', () => {
      const selectedWeekday = selectElement.value;
      const filteredData = filterDataByWeekday(data, selectedWeekday);
      renderChart(filteredData);
  });

  // Trigger chart rendering for the default selected weekday
  const initialWeekday = selectElement.value;
  const filteredData = filterDataByWeekday(data, initialWeekday);
  renderChart(filteredData);
});
