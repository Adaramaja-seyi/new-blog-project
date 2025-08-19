<template>
  <div class="chart-wrapper">
    <canvas ref="chartCanvas"></canvas>
  </div>
</template>

<script>
import Chart from "chart.js/auto";

export default {
  name: "CategoryChart",
  props: {
    data: {
      type: Array,
      default: () => [],
    },
  },
  data() {
    return {
      chart: null,
    };
  },
  watch: {
    data: {
      handler() {
        this.$nextTick(() => {
          this.updateChart();
        });
      },
      deep: true,
    },
  },
  mounted() {
    this.$nextTick(() => {
      this.createChart();
    });
  },
  methods: {
    createChart() {
      try {
        const ctx = this.$refs.chartCanvas;
        if (!ctx) {
          console.warn("Canvas element not found");
          return;
        }

        // Destroy existing chart if it exists
        if (this.chart) {
          this.chart.destroy();
          this.chart = null;
        }

        const chartData = this.prepareChartData();

        this.chart = new Chart(ctx, {
          type: "doughnut",
          data: chartData,
          options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
              legend: {
                position: "bottom",
                labels: {
                  padding: 20,
                  usePointStyle: true,
                  font: {
                    size: 12,
                  },
                  generateLabels: (chart) => {
                    const data = chart.data;
                    if (data.labels.length && data.datasets.length) {
                      return data.labels.map((label, i) => {
                        const dataset = data.datasets[0];
                        const value = dataset.data[i];
                        const total = dataset.data.reduce((a, b) => a + b, 0);
                        const percentage = ((value / total) * 100).toFixed(1);

                        return {
                          text: `${label} (${percentage}%)`,
                          fillStyle: dataset.backgroundColor[i],
                          strokeStyle: dataset.borderColor[i],
                          lineWidth: 2,
                          pointStyle: "circle",
                          hidden: false,
                          index: i,
                        };
                      });
                    }
                    return [];
                  },
                },
              },
              tooltip: {
                backgroundColor: "rgba(0, 0, 0, 0.8)",
                titleColor: "#fff",
                bodyColor: "#fff",
                borderColor: "rgba(255, 255, 255, 0.1)",
                borderWidth: 1,
                cornerRadius: 8,
                callbacks: {
                  label: function (context) {
                    const label = context.label || "";
                    const value = context.parsed;
                    const total = context.dataset.data.reduce(
                      (a, b) => a + b,
                      0
                    );
                    const percentage = ((value / total) * 100).toFixed(1);
                    return `${label}: ${value.toLocaleString()} views (${percentage}%)`;
                  },
                },
              },
            },
            cutout: "60%",
            elements: {
              arc: {
                borderWidth: 2,
                borderColor: "#fff",
              },
            },
          },
        });
      } catch (error) {
        console.error("Error creating chart:", error);
      }
    },
    prepareChartData() {
      const colors = [
        "#0d6efd",
        "#198754",
        "#0dcaf0",
        "#ffc107",
        "#dc3545",
        "#6f42c1",
        "#fd7e14",
        "#20c997",
        "#e83e8c",
        "#6c757d",
      ];

      return {
        labels: this.data.map((item) => item.name),
        datasets: [
          {
            data: this.data.map((item) => item.views),
            backgroundColor: colors.slice(0, this.data.length),
            borderColor: colors.slice(0, this.data.length),
            borderWidth: 2,
            hoverOffset: 4,
          },
        ],
      };
    },
    updateChart() {
      try {
        if (this.chart && this.$refs.chartCanvas) {
          const chartData = this.prepareChartData();
          this.chart.data = chartData;
          this.chart.update("active");
        } else {
          // If chart doesn't exist, create it
          this.createChart();
        }
      } catch (error) {
        console.error("Error updating chart:", error);
      }
    },
  },
  beforeUnmount() {
    try {
      if (this.chart) {
        this.chart.destroy();
        this.chart = null;
      }
    } catch (error) {
      console.error("Error destroying chart:", error);
    }
  },
};
</script>

<style scoped>
.chart-wrapper {
  position: relative;
  height: 100%;
  width: 100%;
}
</style>
