import { mount } from '@vue/test-utils';
import { describe, it, expect } from 'vitest';
import RiskCard from './RiskCard.vue';

describe('RiskCard.vue', () => {
  it('renders score correctly', () => {
    const wrapper = mount(RiskCard, {
      props: { score: 0.65 }
    });
    expect(wrapper.find('span').text()).toBe('0.65');
  });

  it('sets background color to red for high risk', () => {
    const wrapper = mount(RiskCard, {
      props: { score: 0.8 }
    });
    expect(wrapper.get('.risk-card').attributes('style')).toContain('background-color: red');
  });

  it('sets background color to orange for medium risk', () => {
    const wrapper = mount(RiskCard, {
      props: { score: 0.5 }
    });
    expect(wrapper.get('.risk-card').attributes('style')).toContain('background-color: orange');
  });

  it('sets background color to green for low risk', () => {
    const wrapper = mount(RiskCard, {
      props: { score: 0.3 }
    });
    expect(wrapper.get('.risk-card').attributes('style')).toContain('background-color: green');
  });
});
