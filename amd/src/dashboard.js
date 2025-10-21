// Next Theme - Dashboard JavaScript
define(['jquery', 'core/ajax', 'core/notification'], function($, ajax, notification) {
    
    return {
        init: function() {
            this.initCharts();
            this.initFilters();
            this.initAnimations();
        },

        initCharts: function() {
            // Initialize the enrollment chart
            this.createEnrollmentChart();
        },

        createEnrollmentChart: function() {
            const canvas = document.getElementById('enrollmentChart');
            if (!canvas) return;

            const ctx = canvas.getContext('2d');
            
            // Sample data for the chart
            const months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
            const enrollments = [120, 150, 180, 200, 250, 300, 350, 320, 280, 240, 200, 180];
            const completionRates = [75, 78, 80, 82, 85, 88, 85, 87, 84, 81, 79, 77];

            // Create the chart
            this.drawChart(ctx, months, enrollments, completionRates);
        },

        drawChart: function(ctx, months, enrollments, completionRates) {
            const canvas = ctx.canvas;
            const width = canvas.width;
            const height = canvas.height;
            const padding = 40;
            const chartWidth = width - padding * 2;
            const chartHeight = height - padding * 2;

            // Clear canvas
            ctx.clearRect(0, 0, width, height);

            // Set up grid
            ctx.strokeStyle = '#e2e8f0';
            ctx.lineWidth = 1;
            ctx.setLineDash([5, 5]);

            // Draw vertical grid lines
            for (let i = 0; i <= months.length; i++) {
                const x = padding + (i * chartWidth / months.length);
                ctx.beginPath();
                ctx.moveTo(x, padding);
                ctx.lineTo(x, height - padding);
                ctx.stroke();
            }

            // Draw horizontal grid lines
            for (let i = 0; i <= 5; i++) {
                const y = padding + (i * chartHeight / 5);
                ctx.beginPath();
                ctx.moveTo(padding, y);
                ctx.lineTo(width - padding, y);
                ctx.stroke();
            }

            ctx.setLineDash([]);

            // Draw bars for enrollments
            const barWidth = chartWidth / months.length * 0.6;
            const maxEnrollment = Math.max(...enrollments);
            
            enrollments.forEach((value, index) => {
                const barHeight = (value / maxEnrollment) * chartHeight * 0.7;
                const x = padding + (index * chartWidth / months.length) + (chartWidth / months.length - barWidth) / 2;
                const y = height - padding - barHeight;

                // Highlight July bar
                if (index === 6) {
                    ctx.fillStyle = '#3b82f6';
                } else {
                    ctx.fillStyle = '#e2e8f0';
                }

                ctx.fillRect(x, y, barWidth, barHeight);
            });

            // Draw line for completion rates
            ctx.strokeStyle = '#3b82f6';
            ctx.lineWidth = 3;
            ctx.beginPath();

            completionRates.forEach((rate, index) => {
                const x = padding + (index * chartWidth / months.length) + chartWidth / months.length / 2;
                const y = height - padding - (rate / 100) * chartHeight * 0.3;

                if (index === 0) {
                    ctx.moveTo(x, y);
                } else {
                    ctx.lineTo(x, y);
                }
            });

            ctx.stroke();

            // Draw data points
            ctx.fillStyle = '#3b82f6';
            completionRates.forEach((rate, index) => {
                const x = padding + (index * chartWidth / months.length) + chartWidth / months.length / 2;
                const y = height - padding - (rate / 100) * chartHeight * 0.3;

                ctx.beginPath();
                ctx.arc(x, y, 4, 0, 2 * Math.PI);
                ctx.fill();
            });

            // Draw month labels
            ctx.fillStyle = '#64748b';
            ctx.font = '12px Inter, sans-serif';
            ctx.textAlign = 'center';

            months.forEach((month, index) => {
                const x = padding + (index * chartWidth / months.length) + chartWidth / months.length / 2;
                const y = height - padding + 20;
                ctx.fillText(month, x, y);
            });

            // Add hover effect for July
            this.addHoverEffect(ctx, 6, months, enrollments, completionRates, padding, chartWidth, chartHeight);
        },

        addHoverEffect: function(ctx, monthIndex, months, enrollments, completionRates, padding, chartWidth, chartHeight) {
            const canvas = ctx.canvas;
            
            canvas.addEventListener('mousemove', function(e) {
                const rect = canvas.getBoundingClientRect();
                const x = e.clientX - rect.left;
                const y = e.clientY - rect.top;

                // Check if mouse is over July bar
                const barX = padding + (monthIndex * chartWidth / months.length) + (chartWidth / months.length - (chartWidth / months.length * 0.6)) / 2;
                const barWidth = chartWidth / months.length * 0.6;
                const barHeight = (enrollments[monthIndex] / Math.max(...enrollments)) * chartHeight * 0.7;
                const barY = canvas.height - padding - barHeight;

                if (x >= barX && x <= barX + barWidth && y >= barY && y <= barY + barHeight) {
                    // Show tooltip
                    ctx.fillStyle = 'rgba(0, 0, 0, 0.8)';
                    ctx.fillRect(x + 10, y - 60, 150, 50);

                    ctx.fillStyle = 'white';
                    ctx.font = '12px Inter, sans-serif';
                    ctx.textAlign = 'left';
                    ctx.fillText('July 2024', x + 15, y - 40);
                    ctx.fillText('• Enrollments: 350', x + 15, y - 25);
                    ctx.fillText('• Completion: 85.20%', x + 15, y - 10);
                }
            });
        },

        initFilters: function() {
            $('.filter-btn').on('click', function() {
                $('.filter-btn').removeClass('active');
                $(this).addClass('active');
                
                // Here you would typically reload chart data based on the selected filter
                // For now, we'll just show a notification
                notification.addNotification({
                    message: 'Filter changed to: ' + $(this).text(),
                    type: 'info'
                });
            });
        },

        initAnimations: function() {
            // Add staggered animation to stat cards
            $('.stat-card').each(function(index) {
                $(this).css('animation-delay', (index * 0.1) + 's');
            });

            // Add hover animations
            $('.stat-card').hover(
                function() {
                    $(this).addClass('slide-up');
                },
                function() {
                    $(this).removeClass('slide-up');
                }
            );
        },

        // Method to update chart data (can be called from PHP)
        updateChartData: function(data) {
            // This method can be called to update the chart with new data
            console.log('Updating chart with data:', data);
            this.createEnrollmentChart();
        },

        // Method to update stat cards
        updateStats: function(stats) {
            // This method can be called to update the statistics
            console.log('Updating stats with data:', stats);
        }
    };
});
