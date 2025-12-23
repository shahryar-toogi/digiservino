<?php
/* Template Name: Pricing & Plans */
get_header(); ?>

<div class="bg-gray-100 py-20 px-4">
    <div class="max-w-6xl mx-auto text-center">
        <h2 class="text-4xl font-extrabold text-gray-900 mb-4">Service Plans</h2>
        <p class="text-xl text-gray-600 mb-12">Choose the right level of IT support for your business.</p>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            <div class="bg-white rounded-2xl shadow-xl p-8 transform hover:scale-105 transition border-t-8 border-gray-400">
                <h3 class="text-2xl font-bold mb-4">Pay-As-You-Go</h3>
                <div class="text-4xl font-bold mb-6">$49<span class="text-lg text-gray-500 font-normal">/ticket</span></div>
                <ul class="text-left space-y-4 mb-8 text-gray-600">
                    <li>✅ On-demand support</li>
                    <li>✅ Hardware Diagnostics</li>
                    <li>❌ Remote Priority</li>
                    <li>❌ 24/7 Monitoring</li>
                </ul>
                <button class="w-full py-3 px-6 rounded-lg bg-gray-800 text-white font-bold hover:bg-gray-900">Get Started</button>
            </div>

            <div class="bg-white rounded-2xl shadow-2xl p-8 transform hover:scale-110 transition border-t-8 border-indigo-600 relative">
                <div class="absolute top-0 right-0 bg-indigo-600 text-white text-xs px-3 py-1 rounded-bl-lg font-bold">MOST POPULAR</div>
                <h3 class="text-2xl font-bold mb-4">Business Pro</h3>
                <div class="text-4xl font-bold mb-6">$199<span class="text-lg text-gray-500 font-normal">/mo</span></div>
                <ul class="text-left space-y-4 mb-8 text-gray-600">
                    <li>✅ 5 Incident Tickets /mo</li>
                    <li>✅ Priority Remote Support</li>
                    <li>✅ Network Security Audit</li>
                    <li>✅ Monthly Health Report</li>
                </ul>
                <button class="w-full py-3 px-6 rounded-lg bg-indigo-600 text-white font-bold hover:bg-indigo-700 shadow-lg">Upgrade Now</button>
            </div>

            <div class="bg-white rounded-2xl shadow-xl p-8 transform hover:scale-105 transition border-t-8 border-black">
                <h3 class="text-2xl font-bold mb-4">Enterprise</h3>
                <div class="text-4xl font-bold mb-6">$499<span class="text-lg text-gray-500 font-normal">/mo</span></div>
                <ul class="text-left space-y-4 mb-8 text-gray-600">
                    <li>✅ Unlimited Tickets</li>
                    <li>✅ Dedicated Technician</li>
                    <li>✅ 2-Hour Response Time</li>
                    <li>✅ CCTV & Server Mgmt</li>
                </ul>
                <button class="w-full py-3 px-6 rounded-lg bg-black text-white font-bold hover:bg-gray-800">Contact Sales</button>
            </div>
        </div>
    </div>
</div>

<?php get_footer(); ?>