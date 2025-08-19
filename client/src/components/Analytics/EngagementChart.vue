<template>
  <div class="chart-wrapper">
    <canvas ref="chartCanvas"></canvas>
  </div>
</template>

<script>
import Chart from "chart.js/auto";

export default {
  name: "EngagementChart",
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
          type: "line",
          data: chartData,
          options: {
            responsive: true,
            maintainAspectRatio: false,
            interaction: {
              intersect: false,
              mode: "index",
            },
            plugins: {
              legend: {
                display: false,
              },
              tooltip: {
                backgroundColor: "rgba(0, 0, 0, 0.8)",
                titleColor: "#fff",
                bodyColor: "#fff",
                borderColor: "rgba(255, 255, 255, 0.1)",
                borderWidth: 1,
                cornerRadius: 8,
                displayColors: true,
                callbacks: {
                  label: function (context) {
                    let label = context.dataset.label || "";
                    if (label) {
                      label += ": ";
                    }
                    if (context.parsed.y !== null) {
                      label += context.parsed.y.toLocaleString();
                    }
                    return label;
                  },
                },
              },
            },
            scales: {
              x: {
                display: true,
                grid: {
                  display: false,
                },
                ticks: {
                  color: "#6c757d",
                  font: {
                    size: 11,
                  },
                  maxTicksLimit: 10,
                },
              },
              y: {
                display: true,
                grid: {
                  color: "rgba(0, 0, 0, 0.05)",
                  drawBorder: false,
                },
                ticks: {
                  color: "#6c757d",
                  font: {
                    size: 11,
                  },
                  callback: function (value) {
                    if (value >= 1000) {
                      return (value / 1000).toFixed(1) + "K";
                    }
                    return value;
                  },
                },
              },
            },
            elements: {
              point: {
                radius: 4,
                hoverRadius: 6,
                backgroundColor: function (context) {
                  return context.dataset.borderColor;
                },
                borderColor: "#fff",
                borderWidth: 2,
              },
              line: {
                tension: 0.4,
                borderWidth: 3,
              },
            },
          },
        });
      } catch (error) {
        console.error("Error creating chart:", error);
      }
    },
    prepareChartData() {
      const labels = this.data.map((item) => {
        const date = new Date(item.date);
        return date.toLocaleDateString("en-US", {
          month: "short",
          day: "numeric",
        });
      });

      return {
        labels: labels,
        datasets: [
          {
            label: "Views",
            data: this.data.map((item) => item.views),
            borderColor: "#0d6efd",
            backgroundColor: "rgba(13, 110, 253, 0.1)",
            fill: true,
            tension: 0.4,
          },
          {
            label: "Likes",
            data: this.data.map((item) => item.likes),
            borderColor: "#198754",
            backgroundColor: "rgba(25, 135, 84, 0.1)",
            
            fill: true,
            tension: 0.4,
          },
          {
            label: "Comments",
            data: this.data.map((item) => item.comments),
            borderColor: "#0dcaf0",
            backgroundColor: "rgba(13, 202, 240, 0.1)",
            fill: true,
            tension: 0.4,
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
