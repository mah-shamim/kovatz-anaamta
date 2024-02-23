<div
	:class="classes"
	v-if="visible"
>
	<div class="alert-type-line" :style="{ 'background': config.typeBgColor }"></div>
	<div class="alert-icon" v-html="iconHtml"></div>
	<div class="alert-content">
        <div class="alert-title" v-if="title" v-html="title"></div>
        <div class="alert-message" v-if="message" v-html="message"></div>
	</div>
    <div class="alert-buttons" v-if="buttons">
        <cx-vui-button
                v-for="(button, index) in buttons"
                :key="`button-${index}`"
                :class="`cx-vui-button--style-${button.type}`"
                :button-style="button.style"
                size="mini"
                :url="button.url"
                tag-name="a"
                target="_blank"
        >
				<span slot="label">
					<span>{{ button.label }}</span>
				</span>
        </cx-vui-button>
    </div>
	<div
		class="alert-close"
		@click="closeAlert"
	>
		<svg width="20" height="20" viewBox="0 0 14 14" fill="#dcdcdd" xmlns="http://www.w3.org/2000/svg"><path d="M12 3.00671L8.00671 7L12 10.9933L10.9933 12L7 8.00671L3.00671 12L2 10.9933L5.99329 7L2 3.00671L3.00671 2L7 5.99329L10.9933 2L12 3.00671Z"></path></svg>
	</div>
</div>
